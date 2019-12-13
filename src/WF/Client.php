<?php

namespace WF;

use WF\Exception\Trash;
use WF\Stamp\Achievements;

class Client
{
    /**
     * @var string
     */
    const API_EN = 'http://api.wf.my.com/';

    /**
     * @var string
     */
    const API_RU = 'http://api.warface.ru/';

    /**
     * @var array
     */
    const ALLOWABLE = [
        'playtime_h', 'favoritPVE', 'pve_wins', 'favoritPVP', 'pvp_all', 'pvp', 'rank_id', 'clan_name', 'nickname'
    ];

    /**
     * @var $profile
     */
    protected $profile;

    /**
     * @var string $lang
     */
    protected $lang;

    /**
     * @var int $server
     */
    protected $server;

    /**
     * @var string $api
     */
    protected $api;

    /**
     * @var array $achievements
     */
    protected $achievements = [];

    /**
     * @param string $nickname
     * @param int|null $server
     * @return Client
     * @throws Trash
     */
    public function get(string $nickname, int $server): Client
    {
        $this->inspect($server);

        $ch = curl_init(
            $this->api . '/user/stat?' . http_build_query([
                'name' => $nickname,
                'server' => $this->server
            ])
        );

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($ch);

        if (curl_getinfo($ch, CURLINFO_HTTP_CODE) !== 200) {
            throw new Trash('Unable to retrieve player information', 1);
        }

        $this->profile = array_filter(json_decode($data, 1), function ($key) {
            return in_array($key, self::ALLOWABLE);
        }, ARRAY_FILTER_USE_KEY);

        return $this;
    }

    /**
     * @param array $data
     * @return $this
     * @throws Trash
     */
    public function addAchievements(array $data): Client
    {
        $this->achievements = (new Achievements($data))->getList();

        return $this;
    }

    /**
     * @return array
     */
    public function getListAchievements(): array
    {
        return $this->achievements;
    }

    /**
     * @param array $data
     * @return $this
     */
    public function edit(array $data): Client
    {
        foreach ($data as $key => $value) {
            if (in_array($key, self::ALLOWABLE)) {
                $this->profile[$key] = $value;
            }
        }

        return $this;
    }

    /**
     * @param int $value
     * @throws Trash
     */
    private function inspect(int $value): void
    {
        if (!in_array($value, range(1, 5))) {
            throw new Trash('Incorrect server selected');
        }

        $this->server = str_replace([4, 5], [1, 2], $value);
        [$this->lang, $this->api] = in_array($value, [4, 5]) ? ['EN', self::API_EN] : ['RU', self::API_RU];
    }

    /**
     * @return string
     */
    public function getLang(): string
    {
        return $this->lang;
    }

    /**
     * @return int
     */
    public function getServer(): int
    {
        return $this->server;
    }

    /**
     * @return array
     */
    public function getPlayer(): array
    {
        return $this->profile;
    }
}
