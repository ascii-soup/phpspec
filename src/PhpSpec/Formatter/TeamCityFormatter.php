<?php

/*
 * This file is part of PhpSpec, A php toolset to drive emergent
 * design by specification.
 *
 * (c) Marcello Duarte <marcello.duarte@gmail.com>
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PhpSpec\Formatter;

use PhpSpec\Event\SpecificationEvent;
use PhpSpec\Event\SuiteEvent;
use PhpSpec\Event\ExampleEvent;

/**
 * Class TeamCityFormatter
 * @package PhpSpec\Formatter
 */
class TeamCityFormatter extends BasicFormatter
{
    /**
     * @var int
     */
    private $examplesCount = 0;

    private function teamCityMessage($messageType, array $values)
    {
        $message = "##teamcity[{$messageType} ";
        foreach ($values as $key => $value) {
            if (is_string($key)) {
                $message .= $key . "='" . $value . "' ";
            } else {
                $message .= $value;
            }
        }
        return $message . "]";
    }

    /**
     * @param SuiteEvent $event
     */
    public function beforeSuite(SuiteEvent $event)
    {
        $this->examplesCount = count($event->getSuite());
    }

    public function beforeExample(ExampleEvent $event)
    {
        $this->getIO()->writeln($this->teamCityMessage('testStarted', array('name' => $event->getTitle())));
    }

    /**
     * @param ExampleEvent $event
     */
    public function afterExample(ExampleEvent $event)
    {
        $io = $this->getIO();

        $eventsCount = $this->getStatisticsCollector()->getEventsCount();
        if ($eventsCount === 1) {
            $io->writeln();
        }

        switch ($event->getResult()) {
            case ExampleEvent::PASSED:
                break;
            case ExampleEvent::PENDING:
                $io->writeln($this->teamCityMessage('testIgnored', $event->getMessage()));
                break;
            case ExampleEvent::FAILED:
                $io->writeln($this->teamCityMessage('testFailed', $event->getMessage()));
                break;
            case ExampleEvent::BROKEN:
                $io->writeln($this->teamCityMessage('testFailed', $event->getMessage()));
                break;
        }

        $this->getIO()->writeln($this->teamCityMessage('testFinished', array('name' => $event->getTitle())));
    }

    public function beforeSpecification(SpecificationEvent $event)
    {
        $this->getIO()->writeln($this->teamCityMessage('testSuiteStarted', array('name' => $event->getTitle())));
    }

    public function afterSpecification(SpecificationEvent $event)
    {
        $this->getIO()->writeln($this->teamCityMessage('testSuiteFinished', array('name' => $event->getTitle())));
    }


}
