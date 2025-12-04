<?php

namespace App\Http\Controllers\Backend;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\IntercomMarketingDatum;
use Illuminate\Http\Request;
use Grids;
use HTML;
use Illuminate\Support\Facades\Config;
use Nayjest\Grids\Components\Base\RenderableRegistry;
use Nayjest\Grids\Components\ColumnHeadersRow;
use Nayjest\Grids\Components\ColumnsHider;
use Nayjest\Grids\Components\CsvExport;
use Nayjest\Grids\Components\ExcelExport;
use Nayjest\Grids\Components\Filters\DateRangePicker;
use Nayjest\Grids\Components\FiltersRow;
use Nayjest\Grids\Components\HtmlTag;
use Nayjest\Grids\Components\Laravel5\Pager;
use Nayjest\Grids\Components\OneCellRow;
use Nayjest\Grids\Components\RecordsPerPage;
use Nayjest\Grids\Components\RenderFunc;
use Nayjest\Grids\Components\ShowingRecords;
use Nayjest\Grids\Components\TFoot;
use Nayjest\Grids\Components\THead;
use Nayjest\Grids\Components\TotalsRow;
use Nayjest\Grids\DbalDataProvider;
use Nayjest\Grids\EloquentDataProvider;
use Nayjest\Grids\FieldConfig;
use Nayjest\Grids\FilterConfig;
use Nayjest\Grids\Grid;
use Nayjest\Grids\GridConfig;


class IntercomMarketingDataController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $keyword = $request->get('search');
        $perPage = 25;

        if (!empty($keyword)) {
            $intercommarketingdata = IntercomMarketingDatum::
                where('first_name', 'LIKE', "%$keyword%")
                ->orWhere('last_name', 'LIKE', "%$keyword%")
                ->orWhere('name', 'LIKE', "%$keyword%")
                ->orWhere('owner', 'LIKE', "%$keyword%")
                ->orWhere('lead_category', 'LIKE', "%$keyword%")
                ->orWhere('conversation_rating', 'LIKE', "%$keyword%")
                ->orWhere('email', 'LIKE', "%$keyword%")
                ->orWhere('phone', 'LIKE', "%$keyword%")
                ->orWhere('user_uuid', 'LIKE', "%$keyword%")
                ->orWhere('first_seen_date', 'LIKE', "%$keyword%")
                ->orWhere('signed_up_date', 'LIKE', "%$keyword%")
                ->orWhere('last_seen_date', 'LIKE', "%$keyword%")
                ->orWhere('last_contacted_date', 'LIKE', "%$keyword%")
                ->orWhere('last_heard_from_date', 'LIKE', "%$keyword%")
                ->orWhere('last_opened_email_date', 'LIKE', "%$keyword%")
                ->orWhere('last_clicked_on_link_in_email_date', 'LIKE', "%$keyword%")
                ->orWhere('web_sessions', 'LIKE', "%$keyword%")
                ->orWhere('country', 'LIKE', "%$keyword%")
                ->orWhere('region', 'LIKE', "%$keyword%")
                ->orWhere('city', 'LIKE', "%$keyword%")
                ->orWhere('timezone', 'LIKE', "%$keyword%")
                ->orWhere('browser_language', 'LIKE', "%$keyword%")
                ->orWhere('language_override', 'LIKE', "%$keyword%")
                ->orWhere('browser', 'LIKE', "%$keyword%")
                ->orWhere('browser_version', 'LIKE', "%$keyword%")
                ->orWhere('os', 'LIKE', "%$keyword%")
                ->orWhere('twitter_followers', 'LIKE', "%$keyword%")
                ->orWhere('job_title', 'LIKE', "%$keyword%")
                ->orWhere('segment', 'LIKE', "%$keyword%")
                ->orWhere('tag', 'LIKE', "%$keyword%")
                ->orWhere('unsubscribed_from_emails', 'LIKE', "%$keyword%")
                ->orWhere('marked_email_as_spam', 'LIKE', "%$keyword%")
                ->orWhere('has_hard_bounced', 'LIKE', "%$keyword%")
                ->orWhere('utm_campaign', 'LIKE', "%$keyword%")
                ->orWhere('utm_content', 'LIKE', "%$keyword%")
                ->orWhere('utm_medium', 'LIKE', "%$keyword%")
                ->orWhere('utm_source', 'LIKE', "%$keyword%")
                ->orWhere('utm_term', 'LIKE', "%$keyword%")
                ->orWhere('referral_url', 'LIKE', "%$keyword%")
                ->orWhere('job_title', 'LIKE', "%$keyword%")
                ->orWhere('subscribed', 'LIKE', "%$keyword%")
                ->orWhere('pending', 'LIKE', "%$keyword%")
                ->orWhere('unsubscribed', 'LIKE', "%$keyword%")
                ->orWhere('last_connected', 'LIKE', "%$keyword%")
                ->orWhere('canceled_subscription', 'LIKE', "%$keyword%")
                ->orWhere('connected', 'LIKE', "%$keyword%")
                ->orWhere('free_premium', 'LIKE', "%$keyword%")
                ->orWhere('signed_up_appversion', 'LIKE', "%$keyword%")
                ->orWhere('year_1', 'LIKE', "%$keyword%")
                ->orWhere('lifetime_subscription', 'LIKE', "%$keyword%")
                ->orWhere('last_seen_on_iOS_date', 'LIKE', "%$keyword%")
                ->orWhere('iOS_sessions', 'LIKE', "%$keyword%")
                ->orWhere('iOS_app_version', 'LIKE', "%$keyword%")
                ->orWhere('iOS_device', 'LIKE', "%$keyword%")
                ->orWhere('iOS_os_version', 'LIKE', "%$keyword%")
                ->orWhere('last_seen_on_android_date', 'LIKE', "%$keyword%")
                ->orWhere('android_sessions', 'LIKE', "%$keyword%")
                ->orWhere('android_app_version', 'LIKE', "%$keyword%")
                ->orWhere('android_device', 'LIKE', "%$keyword%")
                ->orWhere('android_os_version', 'LIKE', "%$keyword%")
                ->orWhere('enabled_push_messaging', 'LIKE', "%$keyword%")
                ->orWhere('is_mobile_unidentified', 'LIKE', "%$keyword%")
                ->orWhere('company_name', 'LIKE', "%$keyword%")
                ->orWhere('company_id', 'LIKE', "%$keyword%")
                ->orWhere('company_last_seen_date', 'LIKE', "%$keyword%")
                ->orWhere('company_created_at_date', 'LIKE', "%$keyword%")
                ->orWhere('people', 'LIKE', "%$keyword%")
                ->orWhere('company_web_sessions', 'LIKE', "%$keyword%")
                ->orWhere('plan', 'LIKE', "%$keyword%")
                ->orWhere('monthly_spend', 'LIKE', "%$keyword%")
                ->orWhere('company_segment', 'LIKE', "%$keyword%")
                ->orWhere('company_tag', 'LIKE', "%$keyword%")
                ->orWhere('company_size', 'LIKE', "%$keyword%")
                ->orWhere('company_industry', 'LIKE', "%$keyword%")
                ->orWhere('company_website', 'LIKE', "%$keyword%")
                ->orWhere('plan_name', 'LIKE', "%$keyword%")
                ->latest()->paginate($perPage);
        } else {
            $intercommarketingdata = IntercomMarketingDatum::latest()->paginate($perPage);
        }

        return view('backend.intercom-marketing-data.index', compact('intercommarketingdata'));
    }

    public function load_grid(){
        $grid = new Grid(
            (new GridConfig)
                ->setDataProvider(
                    new EloquentDataProvider(IntercomMarketingDatum::query())
                )
                ->setName('intercom')
                ->setPageSize(100)
                ->setColumns([
                    (new FieldConfig)
                        ->setName('id')
                        ->setLabel('ID')
                        ->setSortable(true)
                        ->setSorting(Grid::SORT_ASC)
                    ,
//                    (new FieldConfig)
//                        ->setName('first_name')
//                        ->setLabel('First Name')
////                        ->setCallback(function ($val) {
////                            return "<span class='glyphicon glyphicon-user'></span>{$val}";
////                        })
//                        ->setSortable(true)
//                        ->addFilter(
//                            (new FilterConfig)
//                                ->setOperator(FilterConfig::OPERATOR_LIKE)
//                        )
//                    ,
//                    (new FieldConfig)
//                        ->setName('last_name')
//                        ->setLabel('Last Name')
////                        ->setCallback(function ($val) {
////                            return "<span class='glyphicon glyphicon-user'></span>{$val}";
////                        })
//                        ->setSortable(true)
//                        ->addFilter(
//                            (new FilterConfig)
//                                ->setOperator(FilterConfig::OPERATOR_LIKE)
//                        )
//                    ,

                    (new FieldConfig)
                        ->setName('name')
                        ->setLabel('Name')
//                        ->setCallback(function ($val) {
//                            return "<span class='glyphicon glyphicon-user'></span>{$val}";
//                        })
                        ->setSortable(true)
                        ->addFilter(
                            (new FilterConfig)
                                ->setOperator(FilterConfig::OPERATOR_LIKE)
                        )
                    ,
                    (new FieldConfig)
                        ->setName('email')
                        ->setLabel('Email')
                        ->setSortable(true)
                        ->addFilter(
                            (new FilterConfig)
                                ->setOperator(FilterConfig::OPERATOR_LIKE)
                        )
                    ,
//                    (new FieldConfig)
//                        ->setName('phone')
//                        ->setLabel('Phone')
//                        ->setSortable(true)
//                        ->addFilter(
//                            (new FilterConfig)
//                                ->setOperator(FilterConfig::OPERATOR_LIKE)
//                        )
//                    ,
//                    (new FieldConfig)
//                        ->setName('owner')
//                        ->setLabel('Owner')
//                        ->setSortable(true)
//                        ->addFilter(
//                            (new FilterConfig)
//                                ->setOperator(FilterConfig::OPERATOR_LIKE)
//                        )
//                    ,

                    (new FieldConfig)
                        ->setName('lead_category')
                        ->setLabel('Lead Category')
                        ->setSortable(true)
                        ->addFilter(
                            (new FilterConfig)
                                ->setOperator(FilterConfig::OPERATOR_LIKE)
                        )
                    ,
                    (new FieldConfig)
                        ->setName('conversion_rating')
                        ->setLabel('Conversion Rating')
                        ->setSortable(true)
                        ->addFilter(
                            (new FilterConfig)
                                ->setOperator(FilterConfig::OPERATOR_LIKE)
                        )
                    ,

                    (new FieldConfig)
                        ->setName('user_uuid')
                        ->setLabel('UUID')
                        ->setSortable(true)
                        ->addFilter(
                            (new FilterConfig)
                                ->setOperator(FilterConfig::OPERATOR_LIKE)
                        )
                    ,
                    (new FieldConfig)
                        ->setName('first_seen_date')
                        ->setLabel('Frist Seen')
                        ->setSortable(true)
                        ->addFilter(
                            (new FilterConfig)
                                ->setOperator(FilterConfig::OPERATOR_LIKE)
                        )
                    ,
                    (new FieldConfig)
                        ->setName('signed_up_date')
                        ->setLabel('Singed Up')
                        ->setSortable(true)
                        ->addFilter(
                            (new FilterConfig)
                                ->setOperator(FilterConfig::OPERATOR_LIKE)
                        )
                    ,
                    (new FieldConfig)
                        ->setName('country')
                        ->setLabel('Country')
                        ->setSortable(true)
                        ->addFilter(
                            (new FilterConfig)
                                ->setOperator(FilterConfig::OPERATOR_LIKE)
                        )
                    ,
                    (new FieldConfig)
                        ->setName('region')
                        ->setLabel('Region')
                        ->setSortable(true)
                        ->addFilter(
                            (new FilterConfig)
                                ->setOperator(FilterConfig::OPERATOR_LIKE)
                        )
                    ,
                    (new FieldConfig)
                        ->setName('city')
                        ->setLabel('city')
                        ->setSortable(true)
                        ->addFilter(
                            (new FilterConfig)
                                ->setOperator(FilterConfig::OPERATOR_LIKE)
                        )
                    ,


//                    (new FieldConfig)
//                        ->setName('email')
//                        ->setLabel('Email')
//                        ->setSortable(true)
//                        ->setCallback(function ($val) {
//                            $icon = '<span class="glyphicon glyphicon-envelope"></span>&nbsp;';
//                            return
//                                '<small>'
//                                . $icon
//                                . HTML::link("mailto:$val", $val)
//                                . '</small>';
//                        })
//                        ->addFilter(
//                            (new FilterConfig)
//                                ->setOperator(FilterConfig::OPERATOR_LIKE)
//                        )
//                    ,
//                    (new FieldConfig)
//                        ->setName('phone_number')
//                        ->setLabel('Phone')
//                        ->setSortable(true)
//                        ->addFilter(
//                            (new FilterConfig)
//                                ->setOperator(FilterConfig::OPERATOR_LIKE)
//                        )
//                    ,
//                    (new FieldConfig)
//                        ->setName('country')
//                        ->setLabel('Country')
//                        ->setSortable(true)
//                    ,
//                    (new FieldConfig)
//                        ->setName('company')
//                        ->setLabel('Company')
//                        ->setSortable(true)
//                        ->addFilter(
//                            (new FilterConfig)
//                                ->setOperator(FilterConfig::OPERATOR_LIKE)
//                        )
//                    ,
//                    (new FieldConfig)
//                        ->setName('birthday')
//                        ->setLabel('Birthday')
//                        ->setSortable(true)
//                    ,
//                    (new FieldConfig)
//                        ->setName('posts_count')
//                        ->setLabel('Posts')
//                        ->setSortable(true)
//                    ,
//                    (new FieldConfig)
//                        ->setName('comments_count')
//                        ->setLabel('Comments')
//                        ->setSortable(true)
//                    ,
                ])
                ->setComponents([
                    (new THead)
                        ->setComponents([
                            (new ColumnHeadersRow),
                            (new FiltersRow)
//                                ->addComponents([
//                                    (new RenderFunc(function () {
//                                        return HTML::style('js/daterangepicker/daterangepicker-bs3.css')
//                                            . HTML::script('js/moment/moment-with-locales.js')
//                                            . HTML::script('js/daterangepicker/daterangepicker.js')
//                                            . "<style>
//                                                .daterangepicker td.available.active,
//                                                .daterangepicker li.active,
//                                                .daterangepicker li:hover {
//                                                    color:black !important;
//                                                    font-weight: bold;
//                                                }
//                                           </style>";
//                                    }))
//                                        ->setRenderSection('filters_row_column_birthday'),
//                                    (new DateRangePicker)
//                                        ->setName('birthday')
//                                        ->setRenderSection('filters_row_column_birthday')
//                                        ->setDefaultValue(['1990-01-01', date('Y-m-d')])
//                                ])
                            ,
                            (new OneCellRow)
                                ->setRenderSection(RenderableRegistry::SECTION_END)
                                ->setComponents([
                                    new RecordsPerPage,
                                    new ColumnsHider,
                                    (new CsvExport)
                                        ->setFileName('my_report' . date('Y-m-d'))
                                    ,
                                    new ExcelExport(),
                                    (new HtmlTag)
                                        ->setContent('<span class="glyphicon glyphicon-refresh"></span> Filter')
                                        ->setTagName('button')
                                        ->setRenderSection(RenderableRegistry::SECTION_END)
                                        ->setAttributes([
                                            'class' => 'btn btn-success btn-sm'
                                        ])
                                ])

                        ])
                    ,
                    (new TFoot)
                        ->setComponents([
//                            (new TotalsRow(['posts_count', 'comments_count'])),
//                            (new TotalsRow(['posts_count', 'comments_count']))
//                                ->setFieldOperations([
//                                    'posts_count' => TotalsRow::OPERATION_AVG,
//                                    'comments_count' => TotalsRow::OPERATION_AVG,
//                                ])
//                            ,
                            (new OneCellRow)
                                ->setComponents([
                                    new Pager,
                                    (new HtmlTag)
                                        ->setAttributes(['class' => 'pull-right'])
                                        ->addComponent(new ShowingRecords)
                                    ,
                                ])
                        ])
                    ,
                ])
        );
        $grid = $grid->render();
        //return view('demo.default', compact('grid'));
        return view('backend.intercom-marketing-data.load_grid', compact('grid'));


    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('backend.intercom-marketing-data.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {

        $requestData = $request->all();

        IntercomMarketingDatum::create($requestData);

        return redirect('admin/intercom-marketing-data')->with('flash_message', 'IntercomMarketingDatum added!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     *
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $intercommarketingdatum = IntercomMarketingDatum::findOrFail($id);

        return view('backend.intercom-marketing-data.show', compact('intercommarketingdatum'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     *
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $intercommarketingdatum = IntercomMarketingDatum::findOrFail($id);

        return view('backend.intercom-marketing-data.edit', compact('intercommarketingdatum'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Request $request, $id)
    {

        $requestData = $request->all();

        $intercommarketingdatum = IntercomMarketingDatum::findOrFail($id);
        $intercommarketingdatum->update($requestData);

        return redirect('admin/intercom-marketing-data')->with('flash_message', 'IntercomMarketingDatum updated!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy($id)
    {
        IntercomMarketingDatum::destroy($id);

        return redirect('admin/intercom-marketing-data')->with('flash_message', 'IntercomMarketingDatum deleted!');
    }
}
