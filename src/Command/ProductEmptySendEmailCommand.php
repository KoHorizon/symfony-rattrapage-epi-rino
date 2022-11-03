<?php

namespace App\Command;

use App\Repository\ProductRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

#[AsCommand(
    name: 'app:product-empty:send-email',
    description: 'Send email when product quantity is empty',
)]
class ProductEmptySendEmailCommand extends Command
{
    private ProductRepository $productRepository;
    private MailerInterface $mailer;

    public function __construct(ProductRepository $productRepository, MailerInterface $mailer, string $name = null)
    {
        $this->productRepository = $productRepository;
        $this->mailer = $mailer;
        parent::__construct($name);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $products = $this->productRepository->findBy(
            [
                'quantity' => 0
            ]
        );

        foreach ($products as $product) {
            //$message = (new Email())->from($product->getEmail())->subject('Commande de produit')->.....;
            //$this->mailer->send($message);

            $output->writeln(sprintf('Email pour le produit %s a bien été envoyé', $product->getName()));
        }

        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return Command::SUCCESS;
    }
}
