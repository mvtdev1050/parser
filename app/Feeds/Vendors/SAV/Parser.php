<?php  

namespace App\Feeds\Vendors\SAV;

use App\Feeds\Feed\FeedItem;
use App\Feeds\Parser\HtmlParser;
use App\Helpers\StringHelper;
use App\Feeds\Utils\ParserCrawler;
use Symfony\Component\DomCrawler\Crawler;

class Parser extends HtmlParser{

	private ?string $brand = null;
	private ?float $weight = null;
	private ?string $upc = null;
	private ?array $attrs = null;
	private ?int $avail = null;

	public function beforeParse(): void {

		$this->filter( '#product-attribute-specs-table td' )->each( function ( ParserCrawler $c ) {
			
			if(stripos($c->attr('data-th'),'brand')!==false){
				$this->brand = StringHelper::normalizeSpaceInString($c->text());
			}elseif(stripos($c->attr('data-th'),'Weight') !==false){
				$this->weight = StringHelper::getFloat($c->text());
			}elseif(stripos($c->attr('data-th'),'UPC')!==false){
				$this->upc = StringHelper::normalizeSpaceInString($c->text());
			}else{
				$this->attrs[ StringHelper::normalizeSpaceInString( $c->attr('data-th') ) ] = StringHelper::normalizeSpaceInString( $c->text() );
                
			}
		});

		$this->filter( '.product-info-stock-sku .stock span' )->each( function ( ParserCrawler $c ) {
			if(stripos($c->attr('class'),'label')===false){
				if(stripos(strtolower(StringHelper::normalizeSpaceInString($c->text())),'in stock') !== false){
					$this->avail = self::DEFAULT_AVAIL_NUMBER;
				}else{
					$this->avail = 0;
				}
			}
		});

	}

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

        return $this->avail;
        
    }

    public function getBrand(): ?string {

    	return $this->brand;
    
    }

    public function getWeight(): ?float {

    	return $this->weight;
    
    }

    public function getUpc(): ?string {

    	return $this->upc;
    
    }

    public function getAttributes(): ?array {

        return $this->attrs;
    
    }
}




?>