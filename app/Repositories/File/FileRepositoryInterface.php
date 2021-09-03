<?php

namespace App\Repositories\File;

interface FileRepositoryInterface {
    public function getByFileName($filename);
    public function upload($fileObj);
    public function delete($id);
    public function getFavouriteFiles($user_id);
    public function getTeamFavouriteFiles($team_id);
}
