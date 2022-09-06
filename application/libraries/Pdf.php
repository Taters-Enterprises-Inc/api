<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once(dirname(__FILE__) . '/dompdf/autoload.inc.php');

use Dompdf\Dompdf;
use Dompdf\Options;

class Pdf extends Dompdf
{
    protected function ci()
    {
        return get_instance();
    }

    /**
     * Load a CodeIgniter view into domPDF
     *
     * @access  public
     * @param   string  $view The view to load
     * @param   array   $data The view data
     * @return  void
     */
    public function legalPotrait($view, $data = array())
    {
		$options = new Options();
		$options->set('isRemoteEnabled', true);
        $html = $this->ci()->load->view($view, $data, TRUE);
		$this->setPaper('Legal', 'portrait');
		$this->setOptions($options);
        $this->loadHtml($html);
    }
}
?>
