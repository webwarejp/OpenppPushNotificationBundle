<?php

namespace Openpp\PushNotificationBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Routing\Exception\InvalidParameterException;

/**
 * 
 * @author shiroko@webware.co.jp
 *
 */
class SendNotificationCommand extends ContainerAwareCommand
{
    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this->setName('openpp:push:send')
             ->setDescription('Send the push notification.')
             ->setHelp(<<<EOF
The <info>%command.name%</info> command sends the push notification.
EOF
             )
             ->addArgument('application', InputArgument::REQUIRED, 'Specify the application by its name.')
             ->addArgument('message', InputArgument::REQUIRED, 'Specify the message.')
             ->addArgument('target', InputArgument::OPTIONAL, 'Specify the target by a tag expression.', '')
        ;
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $application = $input->getArgument('application');
        $message     = $input->getArgument('message');
        $target      = $input->getArgument('target');

        $this->getContainer()->get('openpp.push_notification.push_service_manager')->push($application, $target, $message);
    }
}