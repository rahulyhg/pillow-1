<?php if ( ! defined("BASEPATH")) exit("No direct script access allowed");
class Json extends CI_Controller 
{
    //apis by avinash
    
    public function checkdata()
    {
    
        $data = json_decode(file_get_contents('php://input'), true);
        print_r($data);
    }
    public function authenticate()
    {
        $data['message']=$this->user_model->authenticate();
		$this->load->view('json',$data);
    }
   
	public function logout( )
	{
        $this->session->sess_destroy();
		if($this->session->userdata('id')=="")
        {
            $data['message']=true;
        }
        else
        {
            $data['message']=false;
        }
        $this->load->view('json',$data);
//		redirect( base_url() . 'index.php/login', 'refresh' );
	}
    
    public function signup()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $email=$data['email'];
        $password=$data['password'];
        $data['message']=$this->user_model->frontendsignup($email, $password);
        $this->load->view('json',$data);
    }
  
    public function saveimageinuploads()
    {
//        $url = 'http://www.gettyimages.in/gi-resources/images/Homepage/Category-Creative/UK/UK_Creative_462809583.jpg';
        $url = 'https://scontent.cdninstagram.com/hphotos-xaf1/t51.2885-15/s640x640/sh0.08/e35/11351607_1477019669285380_343422983_n.jpg';
        /* parse file and get hostname*/
//        $parse = parse_url($url);
//        print $parse['host'];
//        echo substr($url, 0, 5);
        /* Extract the filename */
//        $timestamp=new DateTime();
//        $timestamp=$timestamp->format('Y-m-d_H.i.s');
//        
//        $filename = substr($url, strrpos($url, '/') + 1);
//        $filename=$timestamp.$filename;
        /* Save file wherever you want */
        $date = new DateTime();
        $filename = "image-".rand(0, 100000)."".$date->getTimestamp().".jpg";
//        echo base_url() .'uploads/'.$filename;
        file_put_contents('uploads/'.$filename, file_get_contents($url));
        echo "<br>".$filename."<br>";
        echo base_url() .'uploads/'.$filename;
    }

    public function createpillow()
    {
        
        $data = json_decode(file_get_contents('php://input'), true);
        $files=$data['image'];
        $userid=$data['userid'];
        
        $orderid=$this->order_model->addorderonproceed($userid);
        $orderproductid=$this->order_model->addorderproductonproceed($orderid);
        foreach($files as $key=>$file)
        {
            $imageurl=$file['img'];
            $order=$key;
            $checkcharacters=substr($imageurl, 0, 5);
            if($checkcharacters=="https")
            {
                echo "in http".$key;
                $date = new DateTime();
                $filename = "image-".rand(0, 100000)."".$date->getTimestamp().".jpg";
                
                file_put_contents('uploads/'.$filename, file_get_contents($imageurl));
                $this->order_model->addorderimageonproceed($orderproductid,$filename,$order);
            }
            else
            {
                echo "in normal".$key;
                $imageurl=$file['img'];
                $this->order_model->addorderimageonproceed($orderproductid,$filename,$order);
            }
        }
        return 1;
    
    }
    
    public function addtocart()
    {
    
        $data = json_decode(file_get_contents('php://input'), true);
        $files=$data['image'];
        $userid=$data['userid'];
        
//        $orderid=$this->order_model->add($userid);
        $orderproductcartid=$this->order_model->addorderproductcartonaddtocart($userid);
        foreach($files as $key=>$file)
        {
            $imageurl=$file['img'];
            $order=$key;
            $checkcharacters=substr($imageurl, 0, 5);
            if($checkcharacters=="https")
            {
                echo "in http".$key;
                $date = new DateTime();
                $filename = "image-".rand(0, 100000)."".$date->getTimestamp().".jpg";
                
                file_put_contents('uploads/'.$filename, file_get_contents($imageurl));
                $this->order_model->adduserproductimagecartonaddtocart($orderproductcartid,$filename,$order);
            }
            else
            {
                echo "in normal".$key;
                $imageurl=$file['img'];
                $this->order_model->adduserproductimagecartonaddtocart($orderproductcartid,$filename,$order);
            }
        }
        return 1;
    }
    
    
    function login() 
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $email=$data["email"];
        $password=$data["password"];
        $data["message"] = $this->user_model->loginuser($email, $password);
        $this->load->view("json", $data);
    }
    //cart functions
    
    
    function getusercart() {
        $user = $this->input->get_post('user');
        $data["message"] = $this->order_model->getusercart($user);
        $this->load->view("json", $data);
    }
//    function addcartsession() {
//        $cart = $this->input->get_post('cart');
//        $data["message"] = $this->order_model->addcartsession($cart);
//        $this->load->view("json", $data);
//    }
    function addtocartold() {
        $user = $this->input->get_post('user');
        $product = $this->input->get_post('product');
        $productname = $this->input->get_post('productname');
        $quantity = $this->input->get_post('quantity');
        $price = $this->input->get_post('price');
//        $image = $this->input->get_post('image');
        $data["message"] = $this->user_model->addtocart($product, $productname, $quantity, $price);
        //$data["message"]=$this->order_model->addtocart($user,$product,$quantity);
        $this->load->view("json", $data);
    }
    function destroycart() {
        $data["message"] = $this->user_model->destroycart();
        $this->load->view("json", $data);
    }
    function showcart() {
        $userid=$this->session->userdata("id");
        if($userid!="")
        {
            $data['message']=$this->user_model->getusercartdetails($userid);
            $this->load->view("json", $data);
        }
        else
        {
            $cart = $this->cart->contents();
            $newcart = array();
            foreach ($cart as $item) {
                array_push($newcart, $item);
            }
            $data["message"] = $newcart;
            $this->load->view("json", $data);
        }
    }
    function totalcart() {
        $data["message"] = $this->cart->total();
        $this->load->view("json", $data);
    }
    function totalitemcart() {
        $data["message"] = $this->cart->total_items();
        $this->load->view("json", $data);
    }
    
    function getorderproductbyuser()
    {
        $userid=$this->input->get("userid");
        $elements=array();
        
        $elements[0]=new stdClass();
        $elements[0]->field="`pillow_orderproduct`.`id`";
        $elements[0]->sort="1";
        $elements[0]->header="ID";
        $elements[0]->alias="id";

        $elements[1]=new stdClass();
        $elements[1]->field="`pillow_orderproduct`.`order`";
        $elements[1]->sort="1";
        $elements[1]->header="Order";
        $elements[1]->alias="order";

        $elements[2]=new stdClass();
        $elements[2]->field="`pillow_orderproduct`.`product`";
        $elements[2]->sort="1";
        $elements[2]->header="Product";
        $elements[2]->alias="product";

        $elements[3]=new stdClass();
        $elements[3]->field="`pillow_orderproduct`.`quantity`";
        $elements[3]->sort="1";
        $elements[3]->header="Quantity";
        $elements[3]->alias="quantity";

        $elements[4]=new stdClass();
        $elements[4]->field="`pillow_orderproduct`.`price`";
        $elements[4]->sort="1";
        $elements[4]->header="Price";
        $elements[4]->alias="price";

        $elements[5]=new stdClass();
        $elements[5]->field="`pillow_orderproduct`.`discount`";
        $elements[5]->sort="1";
        $elements[5]->header="Discount";
        $elements[5]->alias="discount";

        $elements[6]=new stdClass();
        $elements[6]->field="`pillow_orderproduct`.`finalprice`";
        $elements[6]->sort="1";
        $elements[6]->header="Final Price";
        $elements[6]->alias="finalprice";

        $elements[7]=new stdClass();
        $elements[7]->field="`pillow_orderproduct`.`thumbnail`";
        $elements[7]->sort="1";
        $elements[7]->header="Thumbnail";
        $elements[7]->alias="thumbnail";

        $elements[8]=new stdClass();
        $elements[8]->field="`pillow_order`.`email`";
        $elements[8]->sort="1";
        $elements[8]->header="email";
        $elements[8]->alias="email";

        $search=$this->input->get_post("search");
        $pageno=$this->input->get_post("pageno");
        $orderby=$this->input->get_post("orderby");
        $orderorder=$this->input->get_post("orderorder");
        $maxrow=$this->input->get_post("maxrow");
        if($maxrow=="")
        {
            $maxrow="10";
        }
        if($orderby=="")
        {
            $orderby="id";
            $orderorder="ASC";
        }
        $data["message"]=$this->chintantable->query($pageno,$maxrow,$orderby,$orderorder,$search,$elements,"FROM `pillow_orderproduct` LEFT OUTER JOIN `pillow_order` ON `pillow_order`.`id`=`pillow_orderproduct`.`order`","WHERE `pillow_order`.`user`='$userid'");
        $this->load->view("json",$data);
    }
    
    
    
    function getallorders()
    {
        $elements=array();
        
        $elements[0]=new stdClass();
        $elements[0]->field="`pillow_orderproduct`.`id`";
        $elements[0]->sort="1";
        $elements[0]->header="ID";
        $elements[0]->alias="id";

        $elements[1]=new stdClass();
        $elements[1]->field="`pillow_orderproduct`.`order`";
        $elements[1]->sort="1";
        $elements[1]->header="Order";
        $elements[1]->alias="order";

        $elements[2]=new stdClass();
        $elements[2]->field="`pillow_orderproduct`.`product`";
        $elements[2]->sort="1";
        $elements[2]->header="Product";
        $elements[2]->alias="product";

        $elements[3]=new stdClass();
        $elements[3]->field="`pillow_orderproduct`.`quantity`";
        $elements[3]->sort="1";
        $elements[3]->header="Quantity";
        $elements[3]->alias="quantity";

        $elements[4]=new stdClass();
        $elements[4]->field="`pillow_orderproduct`.`price`";
        $elements[4]->sort="1";
        $elements[4]->header="Price";
        $elements[4]->alias="price";

        $elements[5]=new stdClass();
        $elements[5]->field="`pillow_orderproduct`.`discount`";
        $elements[5]->sort="1";
        $elements[5]->header="Discount";
        $elements[5]->alias="discount";

        $elements[6]=new stdClass();
        $elements[6]->field="`pillow_orderproduct`.`finalprice`";
        $elements[6]->sort="1";
        $elements[6]->header="Final Price";
        $elements[6]->alias="finalprice";

        $elements[7]=new stdClass();
        $elements[7]->field="`pillow_orderproduct`.`thumbnail`";
        $elements[7]->sort="1";
        $elements[7]->header="Thumbnail";
        $elements[7]->alias="thumbnail";

        $elements[8]=new stdClass();
        $elements[8]->field="`pillow_order`.`email`";
        $elements[8]->sort="1";
        $elements[8]->header="email";
        $elements[8]->alias="email";

        $search=$this->input->get_post("search");
        $pageno=$this->input->get_post("pageno");
        $orderby=$this->input->get_post("orderby");
        $orderorder=$this->input->get_post("orderorder");
        $maxrow=$this->input->get_post("maxrow");
        if($maxrow=="")
        {
            $maxrow="10";
        }
        if($orderby=="")
        {
            $orderby="id";
            $orderorder="ASC";
        }
        $data["message"]=$this->chintantable->query($pageno,$maxrow,$orderby,$orderorder,$search,$elements,"FROM `pillow_orderproduct` LEFT OUTER JOIN `pillow_order` ON `pillow_order`.`id`=`pillow_orderproduct`.`order`");
        $this->load->view("json",$data);
    }
    
    
    //avinash apis end
    
    
    
    
    
    
    
    
    
    
    
    function getallorder()
    {
    $elements=array();
    $elements[0]=new stdClass();
    $elements[0]->field="`pillow_order`.`id`";
    $elements[0]->sort="1";
    $elements[0]->header="ID";
    $elements[0]->alias="id";

    $elements=array();
    $elements[1]=new stdClass();
    $elements[1]->field="`pillow_order`.`user`";
    $elements[1]->sort="1";
    $elements[1]->header="User";
    $elements[1]->alias="user";

    $elements=array();
    $elements[2]=new stdClass();
    $elements[2]->field="`pillow_order`.`firstname`";
    $elements[2]->sort="1";
    $elements[2]->header="Firstname";
    $elements[2]->alias="firstname";

    $elements=array();
    $elements[3]=new stdClass();
    $elements[3]->field="`pillow_order`.`Lastname`";
    $elements[3]->sort="1";
    $elements[3]->header="Lastname";
    $elements[3]->alias="Lastname";

    $elements=array();
    $elements[4]=new stdClass();
    $elements[4]->field="`pillow_order`.`email`";
    $elements[4]->sort="1";
    $elements[4]->header="Email";
    $elements[4]->alias="email";

    $elements=array();
    $elements[5]=new stdClass();
    $elements[5]->field="`pillow_order`.`billingaddress`";
    $elements[5]->sort="1";
    $elements[5]->header="Billing Address";
    $elements[5]->alias="billingaddress";

    $elements=array();
    $elements[6]=new stdClass();
    $elements[6]->field="`pillow_order`.`billingcity`";
    $elements[6]->sort="1";
    $elements[6]->header="Billing City";
    $elements[6]->alias="billingcity";

    $elements=array();
    $elements[7]=new stdClass();
    $elements[7]->field="`pillow_order`.`billingstate`";
    $elements[7]->sort="1";
    $elements[7]->header="Billing State";
    $elements[7]->alias="billingstate";

    $elements=array();
    $elements[8]=new stdClass();
    $elements[8]->field="`pillow_order`.`billingcountry`";
    $elements[8]->sort="1";
    $elements[8]->header="Billing Country";
    $elements[8]->alias="billingcountry";

    $elements=array();
    $elements[9]=new stdClass();
    $elements[9]->field="`pillow_order`.`shippingaddress`";
    $elements[9]->sort="1";
    $elements[9]->header="Shipping Address";
    $elements[9]->alias="shippingaddress";

    $elements=array();
    $elements[10]=new stdClass();
    $elements[10]->field="`pillow_order`.`shippingcity`";
    $elements[10]->sort="1";
    $elements[10]->header="Shipping City";
    $elements[10]->alias="shippingcity";

    $elements=array();
    $elements[11]=new stdClass();
    $elements[11]->field="`pillow_order`.`shippingcountry`";
    $elements[11]->sort="1";
    $elements[11]->header="Shipping Country";
    $elements[11]->alias="shippingcountry";

    $elements=array();
    $elements[12]=new stdClass();
    $elements[12]->field="`pillow_order`.`shippingstate`";
    $elements[12]->sort="1";
    $elements[12]->header="Shipping State";
    $elements[12]->alias="shippingstate";

    $elements=array();
    $elements[13]=new stdClass();
    $elements[13]->field="`pillow_order`.`shippingpincode`";
    $elements[13]->sort="1";
    $elements[13]->header="Shipping Pincode";
    $elements[13]->alias="shippingpincode";

    $elements=array();
    $elements[14]=new stdClass();
    $elements[14]->field="`pillow_order`.`defaultcurrency`";
    $elements[14]->sort="1";
    $elements[14]->header="Default Currency";
    $elements[14]->alias="defaultcurrency";

    $elements=array();
    $elements[15]=new stdClass();
    $elements[15]->field="`pillow_order`.`totalamount`";
    $elements[15]->sort="1";
    $elements[15]->header="Total Amount";
    $elements[15]->alias="totalamount";

    $elements=array();
    $elements[16]=new stdClass();
    $elements[16]->field="`pillow_order`.`discountamount`";
    $elements[16]->sort="1";
    $elements[16]->header="Discount Amount";
    $elements[16]->alias="discountamount";

    $elements=array();
    $elements[17]=new stdClass();
    $elements[17]->field="`pillow_order`.`finalamount`";
    $elements[17]->sort="1";
    $elements[17]->header="Final Amount";
    $elements[17]->alias="finalamount";

    $elements=array();
    $elements[18]=new stdClass();
    $elements[18]->field="`pillow_order`.`discountcoupon`";
    $elements[18]->sort="1";
    $elements[18]->header="Discount Coupon";
    $elements[18]->alias="discountcoupon";

    $elements=array();
    $elements[19]=new stdClass();
    $elements[19]->field="`pillow_order`.`paymentmethod`";
    $elements[19]->sort="1";
    $elements[19]->header="Payment Method";
    $elements[19]->alias="paymentmethod";

    $elements=array();
    $elements[20]=new stdClass();
    $elements[20]->field="`pillow_order`.`orderstatus`";
    $elements[20]->sort="1";
    $elements[20]->header="Order Status";
    $elements[20]->alias="orderstatus";

    $elements=array();
    $elements[21]=new stdClass();
    $elements[21]->field="`pillow_order`.`currancy`";
    $elements[21]->sort="1";
    $elements[21]->header="Currency";
    $elements[21]->alias="currancy";

    $elements=array();
    $elements[22]=new stdClass();
    $elements[22]->field="`pillow_order`.`trackingcode`";
    $elements[22]->sort="1";
    $elements[22]->header="Tracking Code";
    $elements[22]->alias="trackingcode";

    $elements=array();
    $elements[23]=new stdClass();
    $elements[23]->field="`pillow_order`.`billingpincode`";
    $elements[23]->sort="1";
    $elements[23]->header="Billing Code";
    $elements[23]->alias="billingpincode";

    $elements=array();
    $elements[24]=new stdClass();
    $elements[24]->field="`pillow_order`.`shippingmethod`";
    $elements[24]->sort="1";
    $elements[24]->header="Shipping Method";
    $elements[24]->alias="shippingmethod";

    $elements=array();
    $elements[25]=new stdClass();
    $elements[25]->field="`pillow_order`.`shippingname`";
    $elements[25]->sort="1";
    $elements[25]->header="Shipping Name";
    $elements[25]->alias="shippingname";

    $elements=array();
    $elements[26]=new stdClass();
    $elements[26]->field="`pillow_order`.`shippingtel`";
    $elements[26]->sort="1";
    $elements[26]->header="Shipping Tel";
    $elements[26]->alias="shippingtel";

    $elements=array();
    $elements[27]=new stdClass();
    $elements[27]->field="`pillow_order`.`iscushion`";
    $elements[27]->sort="1";
    $elements[27]->header="Is Cushion";
    $elements[27]->alias="iscushion";

    $search=$this->input->get_post("search");
    $pageno=$this->input->get_post("pageno");
    $orderby=$this->input->get_post("orderby");
    $orderorder=$this->input->get_post("orderorder");
    $maxrow=$this->input->get_post("maxrow");
    if($maxrow=="")
    {
    }
    if($orderby=="")
    {
    $orderby="id";
    $orderorder="ASC";
    }
    $data["message"]=$this->chintantable->query($pageno,$maxrow,$orderby,$orderorder,$search,$elements,"FROM `pillow_order`");
    $this->load->view("json",$data);
    }
    public function getsingleorder()
    {
    $id=$this->input->get_post("id");
    $data["message"]=$this->order_model->getsingleorder($id);
    $this->load->view("json",$data);
    }
    function getallproduct()
    {
    $elements=array();
    $elements[0]=new stdClass();
    $elements[0]->field="`pillow_product`.`id`";
    $elements[0]->sort="1";
    $elements[0]->header="ID";
    $elements[0]->alias="id";

    $elements=array();
    $elements[1]=new stdClass();
    $elements[1]->field="`pillow_product`.`xsize`";
    $elements[1]->sort="1";
    $elements[1]->header="X-Size";
    $elements[1]->alias="xsize";

    $elements=array();
    $elements[2]=new stdClass();
    $elements[2]->field="`pillow_product`.`ysize`";
    $elements[2]->sort="1";
    $elements[2]->header="Y-Size";
    $elements[2]->alias="ysize";

    $elements=array();
    $elements[3]=new stdClass();
    $elements[3]->field="`pillow_product`.`status`";
    $elements[3]->sort="1";
    $elements[3]->header="Status";
    $elements[3]->alias="status";

    $elements=array();
    $elements[4]=new stdClass();
    $elements[4]->field="`pillow_product`.`image`";
    $elements[4]->sort="1";
    $elements[4]->header="Image";
    $elements[4]->alias="image";

    $elements=array();
    $elements[5]=new stdClass();
    $elements[5]->field="`pillow_product`.`name`";
    $elements[5]->sort="1";
    $elements[5]->header="Name";
    $elements[5]->alias="name";

    $search=$this->input->get_post("search");
    $pageno=$this->input->get_post("pageno");
    $orderby=$this->input->get_post("orderby");
    $orderorder=$this->input->get_post("orderorder");
    $maxrow=$this->input->get_post("maxrow");
    if($maxrow=="")
    {
    }
    if($orderby=="")
    {
    $orderby="id";
    $orderorder="ASC";
    }
    $data["message"]=$this->chintantable->query($pageno,$maxrow,$orderby,$orderorder,$search,$elements,"FROM `pillow_product`");
    $this->load->view("json",$data);
    }
    public function getsingleproduct()
    {
    $id=$this->input->get_post("id");
    $data["message"]=$this->product_model->getsingleproduct($id);
    $this->load->view("json",$data);
    }
    function getallorderproduct()
    {
    $elements=array();
    $elements[0]=new stdClass();
    $elements[0]->field="`pillow_orderproduct`.`id`";
    $elements[0]->sort="1";
    $elements[0]->header="ID";
    $elements[0]->alias="id";

    $elements=array();
    $elements[1]=new stdClass();
    $elements[1]->field="`pillow_orderproduct`.`order`";
    $elements[1]->sort="1";
    $elements[1]->header="Order";
    $elements[1]->alias="order";

    $elements=array();
    $elements[2]=new stdClass();
    $elements[2]->field="`pillow_orderproduct`.`product`";
    $elements[2]->sort="1";
    $elements[2]->header="Product";
    $elements[2]->alias="product";

    $elements=array();
    $elements[3]=new stdClass();
    $elements[3]->field="`pillow_orderproduct`.`quantity`";
    $elements[3]->sort="1";
    $elements[3]->header="Quantity";
    $elements[3]->alias="quantity";

    $elements=array();
    $elements[4]=new stdClass();
    $elements[4]->field="`pillow_orderproduct`.`price`";
    $elements[4]->sort="1";
    $elements[4]->header="Price";
    $elements[4]->alias="price";

    $elements=array();
    $elements[5]=new stdClass();
    $elements[5]->field="`pillow_orderproduct`.`discount`";
    $elements[5]->sort="1";
    $elements[5]->header="Discount";
    $elements[5]->alias="discount";

    $elements=array();
    $elements[6]=new stdClass();
    $elements[6]->field="`pillow_orderproduct`.`finalprice`";
    $elements[6]->sort="1";
    $elements[6]->header="Final Price";
    $elements[6]->alias="finalprice";

    $search=$this->input->get_post("search");
    $pageno=$this->input->get_post("pageno");
    $orderby=$this->input->get_post("orderby");
    $orderorder=$this->input->get_post("orderorder");
    $maxrow=$this->input->get_post("maxrow");
    if($maxrow=="")
    {
    }
    if($orderby=="")
    {
    $orderby="id";
    $orderorder="ASC";
    }
    $data["message"]=$this->chintantable->query($pageno,$maxrow,$orderby,$orderorder,$search,$elements,"FROM `pillow_orderproduct`");
    $this->load->view("json",$data);
    }
    public function getsingleorderproduct()
    {
    $id=$this->input->get_post("id");
    $data["message"]=$this->orderproduct_model->getsingleorderproduct($id);
    $this->load->view("json",$data);
    }
    function getallorderproductimage()
    {
    $elements=array();
    $elements[0]=new stdClass();
    $elements[0]->field="`pillow_orderproductimage`.`id`";
    $elements[0]->sort="1";
    $elements[0]->header="ID";
    $elements[0]->alias="id";

    $elements=array();
    $elements[1]=new stdClass();
    $elements[1]->field="`pillow_orderproductimage`.`productimage`";
    $elements[1]->sort="1";
    $elements[1]->header="Product Image";
    $elements[1]->alias="productimage";

    $elements=array();
    $elements[2]=new stdClass();
    $elements[2]->field="`pillow_orderproductimage`.`image`";
    $elements[2]->sort="1";
    $elements[2]->header="Image";
    $elements[2]->alias="image";

    $elements=array();
    $elements[3]=new stdClass();
    $elements[3]->field="`pillow_orderproductimage`.`order`";
    $elements[3]->sort="1";
    $elements[3]->header="Order";
    $elements[3]->alias="order";

    $search=$this->input->get_post("search");
    $pageno=$this->input->get_post("pageno");
    $orderby=$this->input->get_post("orderby");
    $orderorder=$this->input->get_post("orderorder");
    $maxrow=$this->input->get_post("maxrow");
    if($maxrow=="")
    {
    }
    if($orderby=="")
    {
    $orderby="id";
    $orderorder="ASC";
    }
    $data["message"]=$this->chintantable->query($pageno,$maxrow,$orderby,$orderorder,$search,$elements,"FROM `pillow_orderproductimage`");
    $this->load->view("json",$data);
    }
    public function getsingleorderproductimage()
    {
    $id=$this->input->get_post("id");
    $data["message"]=$this->orderproductimage_model->getsingleorderproductimage($id);
    $this->load->view("json",$data);
    }
    
    
    public function imageuploadproduct() 
    {
	    $date = new DateTime();
        $imageName = "image-".rand(0, 100000)."".$date->getTimestamp().".jpg";
        if(move_uploaded_file($_FILES["file"]["tmp_name"], "./uploads/".$imageName)){
       		$data["message"]=$imageName;
            	$this->load->view("json",$data); 
        }else{
        	$data["message"]="false";
            	$this->load->view("json",$data); 
        }
	    
//        $date = new DateTime();
//        $config['upload_path'] = './uploads/';
//        $config['allowed_types'] = 'gif|jpg|png|jpeg';
//        $config['max_size']	= '10000000';
//        $config['overwrite']	= true;
//        $config['file_name']	= "image-".rand(0, 100000)."".$date->getTimestamp();
//
//        $this->load->library('upload', $config);
//        //$image="file";
//        if (  $this->upload->do_upload("file"))
//        {
//            $uploaddata = $this->upload->data();
//            $image=$uploaddata['file_name'];
//
//            $obj = new stdClass();
//            $obj->value=$image;
//            $data["message"]=$obj;
//            $this->load->view("json2",$data); 
//        }
//       else
//        {
//            $obj = new stdClass();
//            $obj->value=$this->upload->display_errors();
//            $data["message"]=$obj;
//            $this->load->view("json2",$data); 
//        }
    }

    
} ?>