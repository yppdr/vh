<?php

class Vhost
{
    protected $domain;
    protected $pathIndex;
    public function __construct($domain, $pathIndex)
    {
        $this->domain  = $domain;
        $this->pathIndex = $pathIndex;
    }
    protected function checkRootDir()
    {
        return is_dir($this->pathIndex);
    }

    /**
    ** créer un fichier .conf
    **/

    protected function confFileContent()
    {
        return "<VirtualHost *:80>"."\n".
                "\t"."ServerName {$this->domain}"."\n".
                "\t"."DocumentRoot {$this->pathIndex}"."\n".
                "\t"."<Directory {$this->pathIndex}>"."\n".
                "\t"."\t"."Options Indexes FollowSymLinks"."\n".
                "\t"."\t"."AllowOverride All"."\n".
                "\t"."\t"."Require all granted"."\n".
                "\t"."</Directory>"."\n".
                "</VirtualHost>"."\n";
    }
    protected function createConfFile($path)
    {
        return file_put_contents($path.$this->domain.'.conf', $this->confFileContent());
    }
    protected function sitesAvailable()
    {
        return $this->createConfFile('/etc/apache2/sites-available/');
    }
    protected function enableSite()
    {
        return `a2ensite {$this->domain}`;
    }
    /**
    ** Host powa :D
    **/
    protected function getHosts()
    {
        return file('/etc/hosts');
    }
    protected function newHost()
    {
        return  array('127.0.0.1       '.$this->domain."\n");
    }
    protected function addNewHost()
    {
        return array_merge($this->newHost(), $this->getHosts());
    }
    protected function hosts()
    {
        return file_put_contents('/etc/hosts', $this->addNewHost());
    }
    /**
    ** Restart Apache comme un fou
    **/
    protected function restartApache()
    {
        return `service apache2 restart`;
    }
    /**
    ** Cree un .vhost
    **/
    public function create()
    {
        if ( $this->checkRootDir() ) {
            $this->sitesAvailable();
            $this->enableSite();
            $this->hosts();
            $this->restartApache();
        } else {
            echo "\n".$this->pathIndex.' le répertoire n\'existe pas'."\n";
        }
    }
}
isset($argv[1]) ? $domain  = $argv[1] : $domain  = '';
isset($argv[2]) ? $pathIndex = $argv[2] : $pathIndex = '';
if ( ! empty($domain) && ! empty($pathIndex) ) {
    $vhost = new Vhost($domain, $pathIndex);
    $vhost->create();
} else {
    echo 'Erreur: nom de domaine et chemin d\'accès au répertoire racine non spécifiés' . "\n";
}
