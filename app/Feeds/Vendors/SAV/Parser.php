<?php  

namespace App\Feeds\Vendors\SAV;

use App\Feeds\Feed\FeedItem;
use App\Feeds\Parser\HtmlParser;
use App\Helpers\StringHelper;
use Symfony\Component\DomCrawler\Crawler;

class Parser extends HtmlParser{
	
	public function getMpn(): string{

		return $this->getText('.product-info-stock-sku .product .sku');
	
	}

	public function getProduct(): string{

		return $this->getText('.page-title span');

	}

	public function getDescription(): string{

		return $this->getHtml('#description .description');

	}

	public function getImages(): array{

		return $this->getSrcImages('.product.media img');

	}

	public function getCostToUs(): float{

		return $this->getMoney('.price-box .old-price span');

	}

	public function getAvail(): ?int {

        return self::DEFAULT_AVAIL_NUMBER;
        
    }
}


?>