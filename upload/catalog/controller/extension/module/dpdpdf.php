<?php
class ControllerExtensionModuleDpdpdf extends Controller {
	public function index() {

		if(isset($this->request->get['pdf'])){ 
			$file = DIR_DOWNLOAD.$this->request->get['pdf'];
			$filename = DIR_DOWNLOAD.$this->request->get['pdf'];
			header('Content-type: application/pdf');
			header('Content-Disposition: inline; filename="' . $filename . '"');
			header('Content-Transfer-Encoding: binary');
			header('Content-Length: ' . filesize($file));
			header('Accept-Ranges: bytes');
			@readfile($file);
		}
	}
}
