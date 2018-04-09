<?php

namespace SlaveMarket\Lease;

use Doctrine\Instantiator\Exception\InvalidArgumentException;
use SlaveMarket\MastersRepository;
use SlaveMarket\SlavesRepository;
use SlaveMarket\Slave;
use SlaveMarket\Master;
use DateInterval;
use DatePeriod;

/**
 * Операция "Арендовать раба"
 *
 * @package SlaveMarket\Lease
 */
class LeaseOperation
{
    /**
     * @var LeaseContractsRepository
     */
    protected $contractsRepository;

    /**
     * @var MastersRepository
     */
    protected $mastersRepository;

    /**
     * @var SlavesRepository
     */
    protected $slavesRepository;


    /**
     * @param DateInterval $interval
     * @param Slave $slave
     * @return float
     */
    protected function priceCalculation(DateInterval $interval, Slave $slave) : float
    {
        return $interval->h == 0 ? (float)$slave->getPricePerHour() : (float)(($interval->h + ($interval->d * 16)) * $slave->getPricePerHour());
    }

    /**
     * @param LeaseHour $firstTime
     * @param LeaseHour $secondTime
     * @return array
     */
    protected function formLeaseHours(LeaseHour $firstTime, LeaseHour $secondTime) : array
    {

        //Костыль, DateInterval не мог пропарсить P1H
        $d1=new \DateTime("2012-07-08 10:14:15");
        $d2=new \DateTime("2012-07-08 11:14:15");
        $d2->diff($d1);

        $leaseHours = array();
        $hours = new DatePeriod($firstTime->getDateTime(), $d2->diff($d1), $secondTime->getDateTime());
        foreach ($hours as $hour) {
            $leaseHours[] = new LeaseHour($hour->format('Y-m-d H'));
        }
        
        return $leaseHours;
    }

    /**
     * @param LeaseRequest $request
     * @param LeaseResponse $response
     */
    protected function makeContract(LeaseRequest $request, LeaseResponse $response)
    {
        $slave = $this->slavesRepository->getById($request->slaveId);

        $firstTime = new LeaseHour($request->timeFrom);
        $secondTime = new LeaseHour($request->timeTo);
        
        $interval = $secondTime->getDateTime()->diff($firstTime->getDateTime());

        $price = $this->priceCalculation($interval, $slave);
        
        $leaseHours = $this->formLeaseHours($firstTime, $secondTime);

        $contract = new LeaseContract(
            $this->mastersRepository->getById($request->masterId),
            $slave,
            $price,
            $leaseHours
        );
        
        $response->setLeaseContract($contract);
    }

    /**
     * @param string $time
     * @return string
     */
    protected function timeModify(string $time) : string
    {
        $timeStart = new \DateTime($time);
        return $timeStart->modify("+30 minutes")->format('Y-m-d H');
    }

    public function busyHoursString(array $leaseHours) : string
    {
        $hours = array();
        foreach ($leaseHours as $leaseHour) {
            $hours[] = $leaseHour->getDateString();
        }
        $hoursString = '"'.implode('", "', $hours).'"';
        return $hoursString;
    }

    /**
     * LeaseOperation constructor.
     *
     * @param LeaseContractsRepository $contractsRepo
     * @param MastersRepository $mastersRepo
     * @param SlavesRepository $slavesRepo
     */
    public function __construct(LeaseContractsRepository $contractsRepo, MastersRepository $mastersRepo, SlavesRepository $slavesRepo)
    {
        $this->contractsRepository = $contractsRepo;
        $this->mastersRepository   = $mastersRepo;
        $this->slavesRepository    = $slavesRepo;
    }

    /**
     * Выполнить операцию
     *
     * @param LeaseRequest $request
     * @return LeaseResponse
     */
    public function run(LeaseRequest $request) : LeaseResponse
    {
        $response = new LeaseResponse();

        $request->timeFrom = $this->timeModify($request->timeFrom);
        $request->timeTo = $this->timeModify($request->timeTo);
        
        $contracts = $this->contractsRepository->getForSlave($request->slaveId, $request->timeFrom, $request->timeTo);

        $slave = $this->slavesRepository->getById($request->slaveId);
        $master = $this->mastersRepository->getById($request->masterId);

        if (empty($contracts)) {
            $this->makeContract($request, $response);
        } else {
            foreach ($contracts as $contract) {
                if ($contract->master->isVIP() || !$master->isVIP()) {
                    $response->addError('Ошибка. Раб #'.$request->slaveId.' "'.$slave->getName().'" занят. Занятые часы: '.$this->busyHoursString($contract->leasedHours));
                }
            }

            if (empty($response->getErrors())){
                $this->makeContract($request, $response);
            }
        }

        return $response;
    }


}