<?php

namespace App\Repositories\Eloquent\Auth;

use App\Models\Auth/Login;
use App\Repositories\LoginRepositoryInterface;
use TimWassenburg\RepositoryGenerator\Repository\BaseRepository;

/**
 * Class LoginRepository.
 */
class LoginRepository extends BaseRepository implements LoginRepositoryInterface
{
    /**
     * UserRepository constructor.
     *
     * @param Auth/Login $model
     */
    public function __construct(Auth/Login $model)
    {
        parent::__construct($model);
    }
}
