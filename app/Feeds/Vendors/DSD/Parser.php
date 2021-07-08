<?php  

namespace App\Feeds\Vendors\DSD;

use App\Feeds\Feed\FeedItem;
use App\Feeds\Parser\HtmlParser;
use App\Helpers\StringHelper;
use App\Feeds\Utils\ParserCrawler;
use Symfony\Component\DomCrawler\Crawler;

class Parser extends HtmlParser{

	
	public function getMpn(): string{
		
		return str_replace(" ","-",$this->getProduct());
	
	}

	public function getProduct(): string{

		return $this->getText('#titletext span');

	} 

	public function getImages(): array{

		return $this->getSrcImages('.columnright form table a[rel="lightbox"] img');

	}

	public function getCostToUs(): float{

		return StringHelper::getMoney( $this->getAttr( 'input[name="price"]', 'value' ) );

	}

	public function getDescription(): string{

		return $this->getHtml('.columnright form table tr td[align="left"] p');

	}

	public function getOptions(): array
    {
        $options = [];
        $option_lists = $this->filter( '.columnright form table input[type="radio"]' );

        if ( !$option_lists->count() ) {
            return $options;
        }

        $option_lists->each( function ( ParserCrawler $list ) use ( &$options ) {
        	$val = $list->attr('value');
			if(str_contains($val, ':')){
				$val = explode(":",$val);
				$size = $val[0];
				$new_price = $val[1]+$this->getCostToUs();
            }else{
            	$new_price = $this->getCostToUs();
            	$size = $val;
            }
            $options[ 'size_'.$size ] = $new_price;
        });
        return $options;
    }


	public function getAvail(): ?int{

        return self::DEFAULT_AVAIL_NUMBER;
    
    }

}

?>