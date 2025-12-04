<?php

namespace App\Repositories;

use App\Models\AppSignupReport;
use App\Repositories\BaseRepository;

/**
 * Class AppSignupReportRepository
 * @package App\Repositories
 * @version January 17, 2021, 2:20 am UTC
*/

class AppSignupReportRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        
    ];

    /**
     * Return searchable fields
     *
     * @return array
     */
    public function getFieldsSearchable()
    {
        return $this->fieldSearchable;
    }

    /**
     * Configure the Model
     **/
    public function model()
    {
        return AppSignupReport::class;
    }
}
