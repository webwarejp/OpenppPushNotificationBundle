<?php

namespace Openpp\PushNotificationBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;

/**
 * 
 * @author shiroko@webware.co.jp
 *
 */
class ConditionSendCommand extends ContainerAwareCommand
{
    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this->setName('openpp:push:condition-send')
             ->setDescription('Send the push notification according to the conditions.')
             ->setHelp(<<<EOF
The <info>%command.name%</info> command sends the push notification according to the conditions.
EOF
             )
             ->addOption('time', null, InputOption::VALUE_OPTIONAL, 'The execution time. (default: "now")', null)
             ->addOption('margin', null, InputOption::VALUE_OPTIONAL, 'The margin of minutes.', null)
        ;
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $time = $input->getOption('time');
        $margin = $input->getOption('margin');

        $conditions = $this->getContainer()->get('openpp.push_notification.task.condition')->execute($time, $margin);

        if (!$conditions) {
            $output->writeln('No conditions match.');
        } else {
            $output->writeln(count($conditions) . ' condition(s) match.');
        }
    }
}