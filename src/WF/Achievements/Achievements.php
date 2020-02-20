<?php

namespace WF\Stamp;

use WF\Exception\Trash;
use WF\Parallel;

class Achievements
{
    /**
     * Catalog of achievements on the site
     */
    const HOST = 'https://wfts.su/wf_achievements/';

    /**
     * @var object $marks
     */
    protected $marks;

    /**
     * @var object $badges
     */
    protected $badges;

    /**
     * @var object $strips
     */
    protected $strips;

    /**
     * @var string|bool $data
     */
    private $data;
    /**
     * @var object $catalog
     */
    private $catalog;

    /**
     * @var array $convert
     */
    protected $convert = [];

    /**
     * Achievements constructor.
     * @param array $data
     * @throws Trash
     */
    public function __construct(array $data)
    {
        $this->catalog = $this->getCatalog();
        $this->spy($data);
    }

    /**
     * @return array
     * @throws Trash
     */
    public function getList(): array
    {
        if (!empty($this->convert)) {
            new Parallel(array_column($this->convert, 'url'), array_column($this->convert, 'dir'));
        }

        return [
            'mark' => $this->marks,
            'badge' => $this->badges,
            'strip' => $this->strips
        ];
    }

    /**
     * @param array $array
     * @throws Trash
     */
    private function spy(array $array): void
    {
        foreach ($array as $key => $value) {
            if (!in_array($key, ['marks', 'badges', 'strips']) || !is_string($key)) {
                throw new Trash('Invalid arguments', 4);
            }

            $this->getAchievements($key, $value);
        }
    }

    /**
     * @param string $type
     * @param int $id
     * @throws Trash
     */
    private function getAchievements(string $type, int $id): void
    {
        $types = $this->catalog->{$type};

        $go = array_search($id, array_column($types, 'id')) ?? false;
        $this->data = $types[$go] ?? false;

        if ($this->data === false || $go === false) {
            throw new Trash('Invalid data', 3);
        }

        $total = __DIR__ . DIRECTORY_SEPARATOR . ucfirst($type) . DIRECTORY_SEPARATOR . $id . '.png';
        $this->{$type} = $total;

        if (!is_file($total)) $this->convert[] = ['url' => self::HOST . $this->data->image, 'dir' => $total];
    }

    /**
     * @return object
     */
    private function getCatalog(): object
    {
        return json_decode(file_get_contents(__DIR__ . '/../Data/achievements.json'))->achievements;
    }
}
