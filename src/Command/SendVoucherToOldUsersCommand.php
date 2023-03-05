<?php

namespace App\Command;

use App\Repository\VoucherRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Client;
use App\Entity\Voucher;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mailer\Transport\Smtp\SmtpTransport;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;
#[AsCommand(
    name: 'send-voucher-to-old-users',
    description: 'Send a voucher to all users registered over a year ago',
)]
class SendVoucherToOldUsersCommand extends Command
{
    private EntityManagerInterface $entityManager;
    private MailerInterface $mailer;
    private VoucherRepository $voucherRepository;
    private Voucher $voucher;

    public function __construct(EntityManagerInterface $entityManager, MailerInterface $mailer, VoucherRepository $voucherRepository)
    {
        $this->entityManager = $entityManager;
        $this->mailer = $mailer;
        $this->voucherRepository = $voucherRepository;

        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $repository = $this->entityManager->getRepository(Client::class);
        $oldClients = $repository->createQueryBuilder('c')
            ->where('c.createdAt < :date')
            ->setParameter('date', new \DateTime('-1 year'))
            ->getQuery()
            ->getResult();

        foreach ($oldClients as $customer) {
            $bytes = random_bytes(12);

            $this->voucher = new Voucher();
            $this->voucher->setAmount(10);          
            $this->voucher->setCode(base_convert(bin2hex($bytes), 16, 36));
            $this->voucher->setType('discount');
            $this->voucher->setClient($customer);

            $this->entityManager->persist($this->voucher);
            $this->entityManager->flush();
            
            $email = (new Email())
                ->from('your_email@example.com')
                ->to($customer->getEmail())
                ->subject('Your subject')
                ->text('Your text message + voucher code: ' . $this->voucher->getCode())
                ->html('<p>Your HTML message</p>');

            $this->mailer->send($email);

            $output->writeln('Voucher created successfully and sent to ' . $customer->getEmail());
        }

        $output->writeln(sprintf('Vouchers sent successfully to %d clients', count($oldClients)));

        $io = new SymfonyStyle($input, $output);

        $io->success('Vouchers sent successfully.');

        return Command::SUCCESS;
    }
}
