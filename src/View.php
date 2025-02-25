<?php
namespace Clessic;

/**
 * ビューを出力するクラス
 */
class View{
	private $name;
	private $extended;
	private $subView;
	
	/**
	 * コンストラクタ
	 * @param string $name ビューの名前
	 */
	public function __construct(string $name){
		$this->name = $name;
		$this->extended = false;
		$this->subView = null;
	}
	
	/**
	 * ビューの出力
	 * @param mixed $args パラメータ
	 * @return mixed ビューから返される値
	 */
	public function __invoke(mixed $args): mixed{
		if($this->extended){
			$params = $this->getData($this->subView, $args);
		}elseif(is_null($this->subView)){
			$params = $args;
		}
		return $this->getViewData($params);
	}
	
	/**
	 * 継承するビューの作成
	 * @param string $name ビューの名前
	 * @return View ビューから返される値
	 */
	public static function extends(string $name){
		$ins = new static($name);
		$ins->extended = true;
		return $ins;
	}
	
	/**
	 * パラメータのアサイン
	 */
	public function assign(mixed ...$args): static{
		if($this->extended){
			$this->subView = $args;
		}
		return $this;
	}
	
	/**
	 * データの取得
	 * 
	 * 指定されたパラメータに基づいてデータを取得し、ビューをレンダリングします。
	 * 
	 * @param array<mixed> $params ビューに渡すパラメータ
	 * @return mixed ビューから返される値
	 */
	private function getViewData(array $params): mixed{
		$data = null;
		$found = Clessic::findView($this->name);
		if(is_string($found)){
			$viewData = require($found);
			$data = $this->getData($viewData, $params);
		}
		return $data;
	}
	
	/**
	 * パラメータの取得
	 * 
	 * @param mixed $viewData パラメータ
	 * @param array<mixed> $params パラメータ
	 * @return mixed 取得したパラメータ
	 */
	private function getData(mixed $viewData, array $params): mixed{
		$data = null;
		if($viewData instanceof self){
			$data = $viewData($params);
		}elseif(is_callable($viewData)){
			ob_start();
			$it = $viewData(...$params);
			if(is_iterable($it)){
				$data = [];
				foreach($it as $a){
					$data[] = ob_get_clean();
					ob_start();
				}
				ob_end_clean();
			}else{
				$data = ob_get_clean();
			}
		}elseif(is_array($viewData)){
			$data = [];
			foreach($viewData as $k => $item){
				if(is_callable($item)){
					ob_start();
					$it = $item(...$params);
					if(is_iterable($it)){
						$data[$k] = [];
						foreach($it as $a){
							$data[$k][] = ob_get_clean();
							ob_start();
						}
						ob_end_clean();
					}else{
						$data[$k] = ob_get_clean();
					}
				}else{
					$data[$k] = $item;
				}
			}
		}else{
			$data = $viewData;
		}
		return $data;
	}
}
