<?php
declare(strict_types=1);
namespace iutnc\deefy\render;
use iutnc\deefy\render\Renderer;
abstract class AudioTrackRenderer implements Renderer{

    public function render(int $selector): string{
        if($selector == self::COMPACT){
            $res = $this->compact();
        }
        if($selector == self::LONG){
            $res = $this->long();
        }
        return $res;
    }

    protected abstract function Compact() : String ;

    protected abstract function Long() : String ;

}