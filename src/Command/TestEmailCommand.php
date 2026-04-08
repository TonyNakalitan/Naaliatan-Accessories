<?php

namespace App\Command;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

#[AsCommand(
    name: 'app:test-email',
    description: 'Test email sending configuration',
)]
class TestEmailCommand extends Command
{
    public function __construct(
        private MailerInterface $mailer,
        private string $mailFromAddress
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('to', InputArgument::REQUIRED, 'Email address to send test email to')
            ->setHelp('This command sends a test email to verify your mailer configuration.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $toEmail = $input->getArgument('to');

        $io->title('Testing Email Configuration');
        $io->section('Configuration');
        $io->text([
            'From: ' . $this->mailFromAddress,
            'To: ' . $toEmail,
        ]);

        try {
            $email = (new TemplatedEmail())
                ->from(new Address($this->mailFromAddress, 'Naaliatan\'s Accessories'))
                ->to($toEmail)
                ->subject('Test Email - Naaliatan\'s Accessories')
                ->htmlTemplate('emails/test_email.html.twig')
                ->context([
                    'timestamp' => new \DateTime(),
                ]);

            $this->mailer->send($email);

            $io->success([
                'Test email sent successfully!',
                'Check your inbox at: ' . $toEmail,
                'Don\'t forget to check spam folder if you don\'t see it.',
            ]);

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $io->error([
                'Failed to send test email!',
                'Error: ' . $e->getMessage(),
            ]);

            $io->section('Troubleshooting Tips');
            $io->listing([
                'Check your MAILER_DSN in .env file',
                'For Gmail: Make sure you\'re using an App Password (not your regular password)',
                'Verify 2-Factor Authentication is enabled on your Gmail account',
                'Check var/log/dev.log for detailed error messages',
                'Try using Mailtrap.io for testing instead',
            ]);

            return Command::FAILURE;
        }
    }
}
