<?php


namespace SamIT\Develop\Commands;


use Github\Api\Repo;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PrepareCommand extends Command
{
    protected $packages = [
        'nginx',
        'php5-fpm',
        'php5-cli',
        'mysql-server',
        'git',
        'ruby-sqlite3',
        'php5-mysqlnd',
        'git-extras',
        'nfs-common',
        'poppler-utils',
        'phantomjs',
        'php5-imagick',
        'php5-curl',
        'beanstalkd',
        'php5-memcached'
    ];

    protected function installAptPackages()
    {
        $parts = [
            'export DEBIAN_FRONTEND=noninteractive',
            'sudo apt-get -qq update',
            "echo 'mysql-server mysql-server/root_password password secret' | sudo debconf-set-selections",
            "echo 'mysql-server mysql-server/root_password_again password secret' | sudo debconf-set-selections",
            "sudo apt-get -y -q install " . implode(' ', $this->packages),
            "sudo apt-get -y -q dist-upgrade"
        ];

        passthru(implode('; ', $parts));
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->installPhpMyAdmin($input, $output);
        $this->installPoshGit($input, $output);
        $this->installAptPackages($input, $output);
    }

    protected function installPhpMyAdmin(InputInterface $in, OutputInterface $out)
    {
        $client = new \Github\Client();
        /** @var Repo $repo */
        $repo = $client->api('repo');
        $data = $repo->releases()->latest('phpmyadmin', 'phpmyadmin');

        $dir = realpath(__DIR__ . '/../runtime') . '/phpmyadmin';
        if (!is_dir($dir)) {
            mkdir($dir);
            $params = [
                '{tarball}' => $data['tarball_url'],
                '{dir}' => $dir
            ];


            $cmd = "curl -L {tarball} | tar xvz --strip 1 -C {dir}";
            passthru(strtr($cmd, $params));


        }
        if (!file_exists("$dir/config.inc.php")) {
            symlink('../../config/phpmyadmin.php', __DIR__ . '/../runtime/phpmyadmin/config.inc.php');
        }
    }

    protected function installPoshGit(InputInterface $in, OutputInterface $out)
    {




        $dir = realpath(__DIR__ . '/../runtime') . '/posh-git-sh';
        if (!is_dir($dir)) {
            mkdir($dir);

            $client = new \Github\Client();
            /** @var Repo $repo */
            $repo = $client->api('repo');
            /** @var string $data The binary tar data. */
            $data = $repo->contents()->archive('lyze', 'posh-git-sh', 'tarball');

            $proc = proc_open("tar xvz --strip 1 -C $dir", [
                ['pipe', 'r'], // 0 is STDIN for process
            ], $pipes);

            fwrite($pipes[0], $data);

        }
    }
}