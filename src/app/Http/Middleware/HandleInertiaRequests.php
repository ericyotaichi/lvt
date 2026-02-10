<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        // 使用 footer 的最后更新时间作为版本号，确保 footer 更新时前端会刷新
        try {
            $footer = \App\Models\Footer::first();
            if ($footer && $footer->updated_at) {
                return md5($footer->updated_at->toDateTimeString());
            }
        } catch (\Throwable $e) {
            // 如果出错，使用默认版本号
        }
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
            ],
            'auth' => [
                'user' => fn () => $request->user()
                    ? $request->user()->only('id', 'name', 'email')
                    : null,
            ],
            'locale' => fn () => app()->getLocale(),
            'footer' => function () {
                try {
                    return \App\Models\Footer::getContent();
                } catch (\Throwable $e) {
                    // 如果数据库连接失败或任何其他错误，返回默认内容
                    return '© ' . date('Y') . ' YourCompany. All rights reserved.';
                }
            },
            'translations' => fn () => [
                'learn_more' => __('common.learn_more'),
                'view_all' => __('common.view_all'),
                'go_to_form' => __('common.go_to_form'),
                'contact_us' => __('common.contact_us'),
                'title' => __('common.home.title'),
                'tagline' => __('common.home.tagline'),
                'company_name' => __('common.home.company_name'),
                'company_slogan' => __('common.home.company_slogan'),
                'about_us' => __('common.home.about_us'),
                'core_technology' => __('common.home.core_technology'),
                'energy_saving' => __('common.home.energy_saving'),
                'energy_saving_desc' => __('common.home.energy_saving_desc'),
                'stable_reliable' => __('common.home.stable_reliable'),
                'stable_reliable_desc' => __('common.home.stable_reliable_desc'),
                'quick_deployment' => __('common.home.quick_deployment'),
                'quick_deployment_desc' => __('common.home.quick_deployment_desc'),
                'core_technology_highlight' => __('common.home.core_technology_highlight'),
                'core_technology_cta' => __('common.home.core_technology_cta'),
                'products_services' => __('common.home.products_services'),
                'applications' => __('common.home.applications'),
                'applications_desc' => __('common.home.applications_desc'),
                'go_to_applications' => __('common.home.go_to_applications'),
                'cases' => __('common.home.cases'),
                'cases_desc' => __('common.home.cases_desc'),
                'go_to_cases' => __('common.home.go_to_cases'),
                'cta_title' => __('common.home.cta_title'),
                'cta_desc' => __('common.home.cta_desc'),
                'products' => [
                    'title' => __('common.products.title'),
                    'description' => __('common.products.description'),
                    'related_applications' => __('common.products.related_applications'),
                ],
                'applications_section' => [
                    'title' => __('common.applications.title'),
                    'description' => __('common.applications.description'),
                    'related_cases' => __('common.applications.related_cases'),
                    'related_products' => __('common.applications.related_products'),
                ],
                'cases_section' => [
                    'title' => __('common.cases.title'),
                    'description' => __('common.cases.description'),
                    'customer' => __('common.cases.customer'),
                    'related_applications' => __('common.cases.related_applications'),
                    'view_details' => __('common.cases.view_details'),
                ],
                'lead' => [
                    'title' => __('common.lead.title'),
                    'alt' => __('common.lead.alt'),
                    'heading' => __('common.lead.heading'),
                    'subheading' => __('common.lead.subheading'),
                    'select_plan' => __('common.lead.select_plan'),
                    'other' => __('common.lead.other'),
                    'name_label' => __('common.lead.name_label'),
                    'phone_label' => __('common.lead.phone_label'),
                    'notes_label' => __('common.lead.notes_label'),
                    'submit' => __('common.lead.submit'),
                    'submitting' => __('common.lead.submitting'),
                ],
                // 扁平 key 作為 fallback，確保英文介面可正確顯示
                'lead_title' => __('common.lead.title'),
                'lead_heading' => __('common.lead.heading'),
                'lead_subheading' => __('common.lead.subheading'),
                'lead_select_plan' => __('common.lead.select_plan'),
                'lead_other' => __('common.lead.other'),
                'lead_name_label' => __('common.lead.name_label'),
                'lead_phone_label' => __('common.lead.phone_label'),
                'lead_notes_label' => __('common.lead.notes_label'),
                'lead_submit' => __('common.lead.submit'),
                'lead_submitting' => __('common.lead.submitting'),
                'no_image' => __('common.no_image'),
                'no_image_uploaded' => __('common.no_image_uploaded'),
                'no_description' => __('common.no_description'),
                'no_content' => __('common.no_content'),
                'no_content_short' => __('common.no_content_short'),
                'view_product' => __('common.view_product'),
                'no_products' => __('common.no_products'),
                'no_applications' => __('common.no_applications'),
                'no_applications_category' => __('common.no_applications_category'),
                'content_preparing' => __('common.content_preparing'),
                'core_tech' => __('common.core_tech'),
                'admin' => [
                    'articles' => [
                        'title' => __('common.admin.articles.title'),
                        'subtitle' => __('common.admin.articles.subtitle'),
                        'create_product' => __('common.admin.articles.create_product'),
                        'search_placeholder' => __('common.admin.articles.search_placeholder'),
                        'all_status' => __('common.admin.articles.all_status'),
                        'search' => __('common.admin.articles.search'),
                        'product' => __('common.admin.articles.product'),
                        'category' => __('common.admin.articles.category'),
                        'status' => __('common.admin.articles.status'),
                        'last_updated' => __('common.admin.articles.last_updated'),
                        'actions' => __('common.admin.articles.actions'),
                        'edit' => __('common.admin.articles.edit'),
                        'no_data' => __('common.admin.articles.no_data'),
                        'no_title' => __('common.admin.articles.no_title'),
                        'none' => __('common.admin.articles.none'),
                    ],
                    'form' => [
                        'create_bundle' => __('common.admin.form.create_bundle'),
                        'edit_bundle' => __('common.admin.form.edit_bundle'),
                        'product' => __('common.admin.form.product'),
                        'product_name' => __('common.admin.form.product_name'),
                        'summary' => __('common.admin.form.summary'),
                        'content' => __('common.admin.form.content'),
                        'sort' => __('common.admin.form.sort'),
                        'status' => __('common.admin.form.status'),
                        'cover' => __('common.admin.form.cover'),
                        'application' => __('common.admin.form.application'),
                        'application_note' => __('common.admin.form.application_note'),
                        'title' => __('common.admin.form.title'),
                        'case' => __('common.admin.form.case'),
                        'case_note' => __('common.admin.form.case_note'),
                        'customer_name' => __('common.admin.form.customer_name'),
                        'draft' => __('common.admin.form.draft'),
                        'published' => __('common.admin.form.published'),
                        'save' => __('common.admin.form.save'),
                        'saving' => __('common.admin.form.saving'),
                        'back_to_list' => __('common.admin.form.back_to_list'),
                        'unsaved_changes' => __('common.admin.form.unsaved_changes'),
                    ],
                ],
            ],
        ]);
    }
}
