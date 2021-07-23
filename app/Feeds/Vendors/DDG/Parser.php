<?php  

namespace App\Feeds\Vendors\DDG;

use App\Feeds\Feed\FeedItem;
use App\Feeds\Parser\HtmlParser;
use App\Helpers\StringHelper;
use App\Feeds\Utils\ParserCrawler;
use Symfony\Component\DomCrawler\Crawler;

class Parser extends HtmlParser{

	private ?array$attributes = null;
	private ?int$stock = self::DEFAULT_AVAIL_NUMBER;
	public function beforeParse(): void{


        if($this->exists( '.columnright form table input[name="optval1"]')){

	        $option_lists = $this->filter( '.columnright form table input[name="optval1"]' );

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
	            if(!str_contains($new_price,'$')){
	            	$this->attributes[ 'size_'.$size ] = array('cost_to_us'=>$new_price,'list_price'=>0);
	            }
	        });
        }else{
        	$this->stock = 0;
        }

        $this->filter( '.columnright form table td[colspan="2"]' )->each( function ( ParserCrawler $c ) {
			$data = explode("<br>",$c->html());
			foreach($data as $val){
				$size = '';
				$old_price = 0;
				$new_price_costtous = 0;
				$new_price_listprice = 0;
				$val = strip_tags( $val );
				if(str_contains($val,'(Was')){
					$new_val = explode(" ",$val);
					if(str_contains($new_val[0],':')){
						$new_p = explode(":",$new_val[0]);
						$size = $new_p[1];
					}else{
						$size = $new_val[0];
					}
					$new_price_costtous = ltrim($new_val[2],'$');
					$new_price_costtous = rtrim($new_val[2],')');
					$new_price_listprice = ltrim($new_val[4],'$');
				}else{
					if(str_contains($val,'$')){
						$new_val = explode(" ",$val);
						if(str_contains($new_val[0],':')){
							$new_p = explode(":",$new_val[0]);
							$size = $new_p[1];
						}else{
							$size = $new_val[0];
						}
						if(array_key_exists(1,$new_val)){
							$new_price_costtous = ltrim($new_val[1],'$');
						}else{
							$new_price_costtous = $new_val[0];
						}
						$new_price_listprice = 0;
					}else{
						$new_price_costtous = $this->getCostToUs();
						$new_price_listprice = 0;
					}
				}
				if($new_price_listprice!==0){
					$old_price = $new_price_costtous;
					$new_price_costtous = $new_price_listprice;
					$new_price_listprice = $old_price;
				}
				if(!str_contains($new_price_costtous,'Price:')){

					$this->attributes[ 'size_'.$size ] = array('cost_to_us'=>$new_price_costtous,'list_price'=>$new_price_listprice);
				}
			}
		});
    }
	
	public function getMpn(): string{
		
		return str_replace(" ","-",$this->getProduct());
	
	}

	public function getProduct(): string{

		return $this->getText('#titletext span');

	} 

	public function getImages(): array{

		$src = $this->getSrcImages('.columnright form table a[rel="lightbox"] img');
		foreach($src as $key=>$img){
			$new_src = explode("?image=",$img);
			$new_src = explode('&width', $new_src[1]);
			$src[$key] = $new_src[0];
		}
		return $src;

	}

	public function getCostToUs(): float{

		return StringHelper::getMoney( $this->getAttr( 'input[name="price"]', 'value' ) );

	}

	public function getDescription(): string{

		return $this->getHtml('.columnright form table tr td[align="left"]');

	}

	/*public function getOptions(): array
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
    }*/


	public function getAvail(): ?int{

        if($this->exists( '.columnright form table input[name="optval1"]') || $this->exists('.columnright form table .pricereg')){
        	return self::DEFAULT_AVAIL_NUMBER;
        }else{
        	return 0;
        }
    
    }

    public function isGroup(): bool
    {
    	return $this->exists( '.columnright form table input[name="optval1"]');
    }

    public function getChildProducts( FeedItem $parent_fi ): array
    {
        $child = [];
        
        unset($this->attributes['size_']);
        
        if($this->getProduct()==='Pink Leopard Sweater'){
			unset($this->attributes['size_Pink']);
		}
		
        foreach($this->attributes as $key=>$value){
        	if(!str_contains($value['cost_to_us'],'Price:')){
    			$new_val = explode("_",$key);
	        	$fi = clone $parent_fi;
	        	$fi->setMpn( str_replace(" ","-",$this->getProduct())."-".$new_val[1] );
	            $fi->setProduct( 'Size: '.$new_val[1]);
	            $fi->setCostToUs( StringHelper::getMoney( $value['cost_to_us'] ) );
	            $fi->setListPrice( StringHelper::getMoney( $value['list_price'] ) );
	            $fi->setRAvail( $this->stock  );
	            $child[] = $fi;
    		}	
        	
        }
        return $child;
    }

}

?>