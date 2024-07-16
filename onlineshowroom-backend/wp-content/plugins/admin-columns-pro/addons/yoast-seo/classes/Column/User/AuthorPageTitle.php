<?php

namespace ACA\YoastSeo\Column\User;

use AC;
use AC\MetaType;
use ACA\YoastSeo\Editing;
use ACA\YoastSeo\Export;
use ACP;
use ACP\Editing\Service\Basic;
use ACP\Editing\Storage\Meta;
use ACP\Editing\View\Text;

class AuthorPageTitle extends AC\Column\Meta
    implements ACP\Editing\Editable, ACP\Search\Searchable, ACP\Sorting\Sortable, ACP\ConditionalFormat\Formattable
{

    use ACP\ConditionalFormat\ConditionalFormatTrait;

    public function __construct()
    {
        $this->set_type('column-yoast_author_title')
             ->set_group('yoast-seo')
             ->set_label(__('SEO Title', 'codepress-admin-columns'));
    }

    private function get_wp_seo_author_title($user_id)
    {
        $old_author = get_query_var('author');
        set_query_var('author', $user_id);

        $titles = get_option('wpseo_titles');
        $author_title = isset($titles['title-author-wpseo']) ? $titles['title-author-wpseo'] : false;
        $title = wpseo_replace_vars($author_title, (object)[]);

        set_query_var('author', $old_author);

        return $title;
    }

    public function get_value($id)
    {
        $title = $this->get_raw_value($id);

        if ( ! $title) {
            $icon = ac_helper()->html->tooltip(
                ac_helper()->icon->dashicon(['icon' => 'media-text', 'class' => 'gray']),
                __('Specific title is missing. The current title is generated by Yoast SEO.', 'codepress-admin-columns')
            );

            $title = sprintf(
                '%s %s',
                $icon,
                $this->get_wp_seo_author_title($id)
            );
        }

        return $title;
    }

    public function get_meta_key()
    {
        return 'wpseo_title';
    }

    public function search()
    {
        return new ACP\Search\Comparison\Meta\Text($this->get_meta_key());
    }

    public function editing()
    {
        return new Basic(new Text(), new Meta($this->get_meta_key(), new MetaType(MetaType::USER)));
    }

    public function sorting()
    {
        return new ACP\Sorting\Model\User\Meta($this->get_meta_key());
    }

}