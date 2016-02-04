<?php


namespace SamIT\Develop\Commands;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class VagrantCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (preg_match('/.*\\\\(?<cmd>\w+)command/', strtolower(get_class($this)), $matches)) {
            passthru("vagrant --color {$matches['cmd']}");
        }
    }
}