<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class LastCommit extends Command
{

    protected const GitHubLastCommitShaUrl = "https://api.github.com/repos/%s/branches/%s";

    protected function configure()
    {
        $this
            // the name of the command (the part after 'bin/console')
            ->setName('app:last-commit')
            // the short description shown while running "php bin/console list"
            ->setDescription('Pokaż ostatni commit.')
            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('Pokazuje ostatni commit.')
            ->addArgument('repository_name', InputArgument::REQUIRED, 'Nazwa repozytorium')
            ->addArgument('branch', InputArgument::REQUIRED, 'Nazwa brancha')
            ->addOption('service', null, InputOption::VALUE_OPTIONAL, 'Nazwa serwisu', 'github');
    }

    //php bin/console app:last-commit paulpiotr/testowe.git master
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $service = $input->getOption('service');
            switch ($service) {
                case 'github':
                    if (($sha = $this->getGitHubLastCommitSha($output, $input->getArgument('repository_name'), $input->getArgument('branch'))) && null !== $sha) {
                        $output->writeln(sprintf("<info> SHA ostatniego commita z brancha <comment>(%s)</comment> to <comment>(%s)</comment> <info>", (string) $input->getArgument('branch'), $sha));
                    } else {
                        $output->writeln(sprintf("<error> Nie można pobrać danych repozytorium (%s) branch (%s) <error>", (string) $input->getArgument('repository_name'), (string) $input->getArgument('branch')));
                    }
                    break;
                default:
                    $output->writeln(sprintf("<error> NIeznany servis (%s) <error>", $service));
                    break;
            }
        } catch (\Exception $e) {
            $output->writeln("<error>{$e->getMessage()}</error>");
            $output->writeln("<error>{$e->getTraceAsString()}</error>");
        }
    }

    protected function getGitHubLastCommitSha(OutputInterface $output, string $repository_name = null, string $branch = null)
    {
        try {
            $url = sprintf(self::GitHubLastCommitShaUrl, $repository_name, $branch);
            $curl_setopt_array = [
                CURLOPT_URL => $url,
                CURLOPT_HEADER => 0,
                CURLOPT_RETURNTRANSFER => TRUE,
                CURLOPT_TIMEOUT => 8,
                CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13',
            ];
            $curl_init = curl_init();
            curl_setopt_array($curl_init, $curl_setopt_array);
            if ($result = curl_exec($curl_init)) {
                $info = curl_getinfo($curl_init);
                if ((int) $info['http_code'] === 200) {
                    $result = json_decode($result, true);
                    curl_close($curl_init);
                    return $result["commit"]["sha"];
                }
            }
            curl_close($curl_init);
            return null;
        } catch (\Exception $e) {
            $output->writeln("<error>{$e->getMessage()}</error>");
            $output->writeln("<error>{$e->getTraceAsString()}</error>");
            return null;
        }
    }
}
