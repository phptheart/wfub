<?php

namespace WF\Stamp;

use WF\Client;

use Imagick;
use ImagickDraw;
use ImagickException;
use ImagickPixel;

class Draw
{
    /**
     * @var string
     */
    const COLOR_YELLOW = '#FFE400';
    /**
     * @var string
     */
    const COLOR_WHITE = '#FFFFFF';

    /**
     * @var object $player
     */
    private $player;

    /**
     * @var Imagick
     */
    private $img;

    /**
     * @var object $obj
     */
    private $obj;

    /**
     * @var mixed $cfg
     */
    private $cfg;

    /**
     * Draw constructor.
     * @param Client $client
     * @throws ImagickException
     */
    public function __construct(Client $client)
    {
        if (!extension_loaded('imagick')) {
            throw new ImagickException('Imagick module not connected');
        }

        $this->cfg = json_decode(file_get_contents(__DIR__ . '/Data/settings.json'));

        $this->player = (object)$client->getPlayer();
        $this->player->server = $client->getServer();
        $this->player->achievements = (object)$client->getListAchievements();

        $this->obj = $this->cfg->{$client->getLang()};
    }

    /**
     * @return Imagick
     * @throws ImagickException
     */
    public function init(): Imagick
    {
        $this->img = new Imagick();
        $this->img->readImageBlob(base64_decode($this->cfg->images->backdrop));

        $this->drawAchievements();
        $this->drawRank();
        $this->drawType();
        $this->drawProfile();
        $this->drawStatistics();

        return $this->img;
    }

    /**
     * @param string $color
     * @param int $size
     * @param bool $static
     * @return ImagickDraw
     */
    private function stamp(string $color, int $size, bool $static = false): ImagickDraw
    {
        $draw = new ImagickDraw();

        $draw->setFillColor(new ImagickPixel($color));
        $draw->setFont(__DIR__ . $this->cfg->fonts->{$static ? 'static' : 'regular'});
        $draw->setFontSize($size);

        return $draw;
    }

    /**
     * @throws ImagickException
     */
    private function drawAchievements(): void
    {
        if (isset($this->player->achievements->strip)) {
            $strip = new Imagick();
            $strip->readImage($this->player->achievements->strip);
            $strip->thumbnailImage(256, 64, 1);

            $this->img->compositeImage($strip, Imagick::COMPOSITE_DEFAULT, 29, 1);
        }

        if (isset($this->player->achievements->badge)) {
            $badge = new Imagick();
            $badge->readImage($this->player->achievements->badge);
            $badge->thumbnailImage(64, 64, 1);

            $this->img->compositeImage($badge, Imagick::COMPOSITE_DEFAULT, 0, 0);
        }

        if (isset($this->player->achievements->mark)) {
            $mark = new Imagick();
            $mark->readImage($this->player->achievements->mark);
            $mark->thumbnailImage(64, 64, 1);

            $this->img->compositeImage($mark, Imagick::COMPOSITE_DEFAULT, 0, 0);
        }
    }

    private function drawProfile(): void
    {
        $offset = 0;

        if (isset($this->player->clan_name)) {
            $clan = $this->stamp(self::COLOR_YELLOW, 12);
            $this->img->annotateImage($clan, 102, 23, 0, $this->player->clan_name);

            $offset = 5;
        }

        $nick = $this->stamp(self::COLOR_WHITE, 14);
        $this->img->annotateImage($nick, 102, 32 + $offset, 0, $this->player->nickname);

        $server = $this->stamp(self::COLOR_WHITE, 12);
        $this->img->annotateImage($server, 102, 45 + $offset, 0, $this->obj->server . $this->obj->data->{$this->player->server});
    }

    private function drawStatistics(): void
    {
        $data = [
            ($this->player->playtime_h ?? 0) . $this->obj->hours,
            $this->player->favoritPVE ? $this->obj->grade->{$this->player->favoritPVE} : $this->obj->not,
            $this->player->pve_wins ?? 0,
            $this->player->favoritPVP ? $this->obj->grade->{$this->player->favoritPVP} : $this->obj->not,
            $this->player->pvp_all ?? 0,
            $this->player->pvp ?? 0
        ];

        $object = $this->stamp(self::COLOR_YELLOW, 5, true);
        $static = 12;

        foreach ($data as $value) {
            $this->img->annotateImage($object, 317, $static += 7, 0, $value);
        }
    }

    /**
     * @throws ImagickException
     */
    private function drawType(): void
    {
        $object = $this->makeType();
        $this->img->compositeImage($object, Imagick::COMPOSITE_DEFAULT, 297, 14);
    }

    /**
     * @throws ImagickException
     */
    private function drawRank(): void
    {
        $object = $this->makeRank($this->player->rank_id);
        $this->img->compositeImage($object, Imagick::COMPOSITE_DEFAULT, 64, 18);
    }

    /**
     * @return Imagick
     * @throws ImagickException
     */
    public function makeType(): Imagick
    {
        $image = new Imagick();
        $image->readImageBlob(base64_decode($this->obj->path));

        return $image;
    }

    /**
     * @param int $rank
     * @return Imagick
     * @throws ImagickException
     */
    public function makeRank(int $rank): Imagick
    {
        $image = new Imagick();
        $image->readImageBlob(base64_decode($this->cfg->images->ranks));
        $image->cropImage(32, 32, 0, ($rank - 1) * 32);

        return $image;
    }
}