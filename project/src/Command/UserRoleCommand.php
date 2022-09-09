<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

#[AsCommand(
    name: 'app:user:role',
    description: 'Ajoutez un rôle à l\'utilisateur de votre choix:',
)]
class UserRoleCommand extends Command
{
    private $em;


    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        parent::__construct();

    }

    protected function configure(): void
    {
        $this
            ->setDescription('Ajouter un rôle à un utilisateur :')
            ->addArgument('email', InputArgument::OPTIONAL, 'Adresse e-mail de votre utilisateur :')
            ->addArgument('roles', InputArgument::OPTIONAL, 'Le rôle défini :');
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $helper = $this->getHelper('question');

        $emailQ = new Question('Adresse e-mail de votre utilisateur :');
        $rolesQ = new Question('Le rôle défini :[ROLE_USER]','ROLE_USER');

        $email = $helper->ask($input, $output, $emailQ);
        $roles = $helper->ask($input, $output, $rolesQ);

        if(!$email)
            $email = $input->getArgument('email');
        if(!$roles)
            $roles = $input->getArgument('roles');

        $output->writeln([
            '============',
            'Ajoutez un rôle à l\'utilisateur de votre choix',
            '============',
            "Email {$email}",
            '============',
            "roles {$roles}",
            '============',
        ]);

        $userRP = $this->em->getRepository(User::class);
        $user = $userRP->findOneByEmail($email);

        if($user){
            $user->addRoles($roles);
            $this->em->flush();
            $io->success("Le rôle {$roles} a été ajouté à votre utilisateur : ".$email);
            return Command::SUCCESS;
        }else {
            $io->error("Aucun utilisateur associé au mail {$email}");
            return Command::FAILURE;
        }
        
    }
}
