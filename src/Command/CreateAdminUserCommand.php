<?php

namespace App\Command;



use App\Entity\User;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ContainerInterface;

class CreateAdminUserCommand extends Command
{
    protected static $defaultName = 'guestbook:add-admin';

    private $container;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct();
        $this->container = $container;
    }

    protected function configure()
    {
        $this
            ->setDescription('Add new admin user')
            ->addArgument('user', InputArgument::REQUIRED, 'Type Username')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->container->get('doctrine')->getManager();
        $io = new SymfonyStyle($input, $output);
        $user = $input->getArgument('user');

        if ($user) {
            $userEntity = $em->getRepository(User::class)->findOneBy(['username' => $user]);
            if(!is_null($userEntity)) {
                $userEntity->setRoles(['ROLE_ADMIN']);
                $em->persist($userEntity);
                $em->flush();
                $io->success('Admin '.$user.' has been added!');
            }
            else {
                $io->error('User not found');
            }
        }
        else {
            $io->error('Missing argument "user"');
        }

    }
}