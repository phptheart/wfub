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
     * @var string containing the color Yellow
     */
    const COLOR_YELLOW = '#FFE400';
    /**
     * @var string containing the color White
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

        $this->player = $client->getPlayer();
        $this->player->server = $client->getServer();

        $this->obj = $this->cfg->{$client->getLang()};
    }

    /**
     * Initializing and rendering the final image
     *
     * @return Imagick
     * @throws ImagickException
     */
    public function init(): Imagick
    {
        $this->img = new Imagick();
        $this->img->readImageBlob(base64_decode($this->cfg->images->backdrop));

        $this->drawRank();
        $this->drawType();
        $this->drawProfile();
        $this->drawStatistics();

        $this->img->setImageFormat('png');

        return $this->img;
    }

    /**
     * Rendering master data
     *
     * @return Imagick
     */
    private function drawProfile(): Imagick
    {
        $offset = 0;

        if (isset($this->player->clan_name)) {
            $objClan = $this->stamp(self::COLOR_YELLOW, 12);
            $this->img->annotateImage($objClan, 102, 23, 0, $this->player->clan_name);

            $offset = 5;
        }

        $objNick = $this->stamp(self::COLOR_WHITE, 14);
        $this->img->annotateImage($objNick, 102, 32 + $offset, 0, $this->player->nickname);

        $objServer = $this->stamp(self::COLOR_WHITE, 12);
        $this->img->annotateImage($objServer, 102, 45 + $offset, 0, $this->obj->server . $this->obj->data->{$this->player->server});

        return $this->img;
    }

    /**
     * Game statistics rendering function
     *
     * @return Imagick
     */
    private function drawStatistics(): Imagick
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

        return $this->img;
    }

    /**
     * Forms an ImageDraw object to use overlays
     *
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
     * Rendering of the concept type
     *
     * @return Imagick
     * @throws ImagickException
     */
    private function drawType(): Imagick
    {
        $object = $this->makeType();
        $this->img->compositeImage($object, Imagick::COMPOSITE_DEFAULT, 297, 14);

        return $this->img;
    }

    /**
     * Rank rendering
     *
     * @return Imagick
     * @throws ImagickException
     */
    private function drawRank(): Imagick
    {
        $object = $this->makeRank($this->player->rank_id);
        $this->img->compositeImage($object, Imagick::COMPOSITE_DEFAULT, 64, 18);

        return $this->img;
    }

    /**
     * Finding the right type concept
     *
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
     * Finding the right rank to print
     *
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