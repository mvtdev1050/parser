<?php 
namespace App\Feeds\Vendors\SAV;

use App\Feeds\Processor\SitemapHttpProcessor;
use App\Feeds\Feed\FeedItem;
use App\Feeds\Utils\Data;
use App\Feeds\Utils\Link;



class Vendor extends SitemapHttpProcessor{

	public array $first = [
		'https://www.savedollarstores.com/sitemap-1-1.xml',
		/*'https://www.savedollarstores.com/sitemap-1-12.xml',
		'https://www.savedollarstores.com/sitemap-1-3.xml',
		'https://www.savedollarstores.com/sitemap-1-4.xml',
		'https://www.savedollarstores.com/sitemap-1-5.xml',
		'https://www.savedollarstores.com/sitemap-1-6.xml',
		'https://www.savedollarstores.com/sitemap-1-7.xml',
		'https://www.savedollarstores.com/sitemap-1-8.xml',
		'https://www.savedollarstores.com/sitemap-1-9.xml',
		'https://www.savedollarstores.com/sitemap-1-10.xml',
		'https://www.savedollarstores.com/sitemap-1-11.xml',
		'https://www.savedollarstores.com/sitemap-1-12.xml',
		'https://www.savedollarstores.com/sitemap-1-13.xml',
		'https://www.savedollarstores.com/sitemap-1-14.xml',*/
	];

	protected ?int $max_products = 10;

	public const CATEGORY_LINK_CSS_SELECTORS = ['url loc'];

	public const PRODUCT_LINK_CSS_SELECTORS = ['loc'];
		
	public function getCategoriesLinks( Data $data, string $url ): array{
        return array_map(
            static fn( Link $link ) => new Link( $link->getUrl()),
            parent::getCategoriesLinks( ...func_get_args() )
        );
    }

	protected function isValidFeedItem( FeedItem $fi ): bool{
		return $fi->getCostToUs() > 0;
	}

}

?>