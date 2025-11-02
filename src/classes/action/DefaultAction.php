<?php
declare(strict_types=1);
namespace iutnc\deefy\action;
use iutnc\deefy\action\action;
class DefaultAction extends Action{

    public function __invoke(): string {
        return "<h1>Bienvenue !</h1>";
    }



}