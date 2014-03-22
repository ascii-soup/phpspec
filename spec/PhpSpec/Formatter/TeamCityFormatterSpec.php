<?php

namespace spec\PhpSpec\Formatter;

use PhpSpec\Console\IO;
use PhpSpec\Event\SpecificationEvent;
use PhpSpec\Formatter\Presenter\PresenterInterface;
use PhpSpec\IO\IOInterface;
use PhpSpec\Listener\StatisticsCollector;
use PhpSpec\Loader\Node\SpecificationNode;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class TeamCityFormatterSpec extends ObjectBehavior
{
    function let(
        PresenterInterface $presenter,
        IO $io,
        StatisticsCollector $stats
    ) {
        $this->beConstructedWith($presenter, $io, $stats);
    }

    function it_should_announce_the_start_of_a_specification(IO $io, SpecificationEvent $specificationEvent, SpecificationNode $specification, \ReflectionClass $reflectionClass)
    {
        $specificationEvent->getSpecification()->willReturn($specification);
        $specification->getClassReflection()->willReturn($reflectionClass);
        $reflectionClass->getFileName()->willReturn('TestSpec.php');
        $specificationEvent->getTitle()->willReturn('SpecificationOne');
        $io->writeln("##teamcity[testSuiteStarted name='SpecificationOne' locationHint='php_qn://TestSpec.php::\\SpecificationOne' ]")->shouldBeCalled();
        $this->beforeSpecification($specificationEvent);
    }

    function it_should_ask_me_about_methods_that_dont_exist()
    {
        $this->notAnActualMethod()->shouldReturn(1);
    }
}
