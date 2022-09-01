<?php
namespace App\Service;

use Psr\Log\LoggerInterface;

class Proverbe{

    public $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

        public function getProverbe():mixed{
        $messages = [
            "Le bonheur ne s'acquiert pas, il ne réside pas dans les apparences, chacun d'entre nous le construit à chaque instant de sa vie avec son coeur.",
            "Mieux vaut vivre un jour comme un lion que cent ans comme un mouton.",
            "Un bon père de famille doit être partout, Dernier couché premier debout.",
            "Quand la pauvreté entre par la porte, l'amour s'en va par la fenêtre.",
            "Quand tout va bien on peut compter sur les autres, quand tout va mal on ne peut compter que sur sa famille.",
            "Aime-toi et tu auras des amis.",
            "Avec du temps et de la patience, on vient à bout de tout.",
            "Quand on achète une maison, on regarde les poutres ; quand on prend une femme, il faut regarder la mère."
        ];
        $index = array_rand($messages);
        $this->logger->info('Le proverbe a été vu');
        return $messages[$index];
    }   
}