<?php
/**
 * FoodCoopShop - The open source software for your foodcoop
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @since         FoodCoopShop 1.0.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 * @author        Mario Rothauer <office@foodcoopshop.com>
 * @copyright     Copyright (c) Mario Rothauer, http://www.rothauer-it.com
 * @link          https://www.foodcoopshop.com
 */
use Cake\Core\Configure;

$this->element('addScript', ['script' =>
    Configure::read('app.jsNamespace').".Helper.init();".
    Configure::read('app.jsNamespace').".AppFeatherlight.initLightboxForImages('.blog_posts.detail .img-wrapper a');"
]);
?>

<h1><?php echo $title_for_layout; ?></h1>

<?php
    echo '<div class="img-wrapper">';
        $srcLargeImage = $this->Html->getBlogPostImageSrc($blogPost->id_blog_post, 'single');
        $largeImageExists = preg_match('/no-single-default/', $srcLargeImage);
if (!$largeImageExists) {
    echo '<a href="'.$srcLargeImage.'">';
    echo '<img class="blog-post-image" src="' . $this->Html->getBlogPostImageSrc($blogPost->id_blog_post, 'single'). '" />';
    echo '</a>';
}
    echo '</div>';

if ($blogPost->short_description != '') {
    echo '<p><b>'.$blogPost->short_description.'</b></p>';
}

    echo $blogPost->content;

    echo '<p><i>';
        echo '<br />'.__('Modified_on'). ' ' . $blogPost->modified->i18nFormat(Configure::read('DateFormat.de.DateNTimeShort'));
if (!empty($blogPost->manufacturer)) {
    echo '<br />';
    if ($blogPost->manufacturer->active) {
        echo '<a href="'.$this->Slug->getManufacturerBlogList($blogPost->manufacturer->id_manufacturer, $blogPost->manufacturer->name).'">'.__('Go_to_blog_from') . ' ' . $blogPost->manufacturer->name.'</a>';
    } else {
        echo __('by') . ' ' . $blogPost->manufacturer->name;
    }
}
    echo '</i></p>';
    echo '<div class="sc"></div>';

if ($appAuth->isSuperadmin() || $appAuth->isAdmin()) {
    echo $this->Html->getJqueryUiIcon(
        $this->Html->image($this->Html->getFamFamFamPath('page_edit.png')),
        [
        'title' => __('Edit')
        ],
        $this->Slug->getBlogPostEdit($blogPost->id_blog_post)
    );
}

?>

<?php
if (!empty($neighbors['prev']) || !empty($neighbors['next'])) {
    echo '<h2>Weitere Beiträge</h2>';
}
if (!empty($neighbors['prev'])) {
    echo $this->element('blogPosts', [
    'blogPosts' => [$neighbors['prev']],
    'useCarousel' => false,
    'style' => 'float: left;'
    ]);
}
if (!empty($neighbors['next'])) {
    echo $this->element('blogPosts', [
        'blogPosts' => [$neighbors['next']],
        'useCarousel' => false,
        'style' => 'float: right;'
    ]);
}
?>
