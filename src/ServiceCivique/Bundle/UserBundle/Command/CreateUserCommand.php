<?php

namespace ServiceCivique\Bundle\UserBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use FOS\UserBundle\Command\CreateUserCommand as BaseCreateUserCommand;

use ServiceCivique\Bundle\UserBundle\Entity\User;

class CreateUserCommand extends BaseCreateUserCommand
{
    protected function configure()
    {
        $this->setName('service_civique:user:create')
            ->setDescription('Create a user')
            ->setDefinition([
                new InputArgument('email', InputArgument::REQUIRED, 'The email'),
                new InputArgument('password', InputArgument::REQUIRED, 'The password'),
                new InputOption('inactive', null, InputOption::VALUE_NONE, 'Set the user as inactive'),
                new InputOption('type', null, InputOption::VALUE_OPTIONAL, ''),
                new InputOption('gender', null, InputOption::VALUE_OPTIONAL, ''),
                new InputOption('firstname', null, InputOption::VALUE_OPTIONAL, ''),
                new InputOption('lastname', null, InputOption::VALUE_OPTIONAL, '')
            ]);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $email      = $input->getArgument('email');
        $password   = $input->getArgument('password');
        $inactive   = $input->getOption('inactive');

        $username = $email;

        $datas = array(
            'firstname' => $input->getOption('firstname'),
            'lastname'  => $input->getOption('lastname'),
            'gender'    => $input->getOption('gender')
        );

        $type = $input->getOption('type');

        $manipulator = $this->getContainer()->get('service_civique_user.util.user_manipulator');
        $manipulator->create($username, $password, $email, !$inactive, false, $type, $datas);

        $output->writeln(sprintf('Created user <comment>%s</comment>', $username));
    }

    /**
     * @see Command
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $this->interactHelper($input, $output, 'type', 'option');
        $this->interactHelper($input, $output, 'email');
        $this->interactHelper($input, $output, 'password');

        if (in_array($input->getOption('type'), array(
            User::MISSION_SEEKER_TYPE,
            User::FORMER_VOLUNTEER_TYPE,
            User::VOLUNTEER_TYPE
        ))) {
            $this->interactJeune($input, $output);
        }
    }

    protected function interactJeune(InputInterface $input, OutputInterface $output)
    {
        $this->interactHelper($input, $output, 'firstname', 'option');
        $this->interactHelper($input, $output, 'lastname', 'option');
        $this->interactHelper($input, $output, 'gender', 'option');
    }

    private function interactHelper($input, $output, $option_name, $type = 'argument', $callback = null)
    {
        $type = ucfirst($type);

        if (!$callback) {
            $callback = function ($value) {
                if (empty($value)) {
                    throw new \Exception('firstname can not be empty');
                }

                return $value;
            };
        }

        if (!$input->{'get' . ucfirst($type)}($option_name)) {
            $value = $this->getHelper('dialog')->askAndValidate(
                $output,
                'Please choose a '.$option_name.':',
                $callback
            );
            $input->{'set' . ucfirst($type)}($option_name, $value);
        }
    }
}
