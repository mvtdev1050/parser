<?php 
namespace App\Feeds\Vendors\DSD;

use App\Feeds\Processor\SitemapHttpProcessor;
use App\Feeds\Feed\FeedItem;
use App\Feeds\Utils\Data;
use App\Feeds\Utils\Link;


class Vendor extends SitemapHttpProcessor{

	public array $first = [
		'https://www.dallasdogs.com/sitemap.xml',
	];

	protected ?int $max_products = 10;

	public const CATEGORY_LINK_CSS_SELECTORS = ['url loc'];

	public const PRODUCT_LINK_CSS_SELECTORS = ['loc'];
	
	public function filterProductLinks( Link $link ): bool {
		return str_contains( $link->getUrl(), 'item_' );
    }

}

?>