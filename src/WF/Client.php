<?php

namespace WF;

use WF\Exception\Trash;

class Client
{
    /**
     * @var string API link in English
     */
    const API_EN = 'http://api.wf.my.com/';

    /**
     * @var string API link in Russian
     */
    const API_RU = 'http://api.warface.ru/';

    /**
     * The variable which contains json string data obtained with the help of API
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
     * Getting data from API
     *
     * @param string $nickname
     * @param int|null $server
     * @return Client
     * @throws Trash
     */
    public function get(string $nickname, int $server): Client
    {
        $ch = curl_init(
            $this->inspect($server) . '/user/stat?' . http_build_query([
                'name' => $nickname,
                'server' => $this->server
            ])
        );

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $this->profile = curl_exec($ch);

        if (curl_getinfo($ch, CURLINFO_HTTP_CODE) !== 200) {
            throw new Trash('Unable to retrieve player information', 1);
        }

        return $this;
    }

    /**
     * Allows you to edit the specific data of the game profile, further reflected on the userbar
     *
     * @param array $data
     * @return $this
     */
    public function edit(array $data): Client
    {
        $content = json_decode($this->profile);

        $let = [
            'playtime_h', 'favoritPVE', 'pve_wins', 'favoritPVP',
            'pvp_all', 'pvp', 'rank_id', 'clan_name', 'nickname'
        ];

        foreach ($data as $key => $value) {
            if ($value === false)
                continue;

            if (in_array($key, $let)) {
                $content->{$key} = $value;
            }
        }

        $this->profile = json_encode($content);

        return $this;
    }

    /**
     * Determines and adjusts the desired server depending on the language
     *
     * @param int $value
     * @return string
     * @throws Trash
     */
    private function inspect(int $value): string
    {
        $logic = in_array($value, [4, 5]);

        if (in_array($value, range(1, 5))) {
            $this->lang = $logic ? 'EN' : 'RU';
            $this->server = str_replace([4, 5], [1, 2], $value);

            return $logic ? self::API_EN : self::API_RU;
        } else throw new Trash('Incorrect server selected');
    }

    /**
     * Returns a string with the selected language
     *
     * @return string
     */
    public function getLang(): string
    {
        return $this->lang;
    }

    /**
     * Returns the ID of the received server
     *
     * @return int
     */
    public function getServer(): int
    {
        return $this->server;
    }

    /**
     * Returns the game profile data object
     *
     * @return object
     */
    public function getPlayer(): object
    {
        return json_decode($this->profile);
    }
}
