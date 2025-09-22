<?php

/*
 * Author Rudyuk Vitalij Anatolievich
 * Email rvansp@gmail.com
 * Blog www.cervic.info
 */
?>
<?php

class Infomodus_Upslabel_Block_Adminhtml_Upslabel_Ups
{

    protected $AccessLicenseNumber;
    protected $UserId;
    protected $Password;
    protected $shipperNumber;
    protected $credentials;
    public $dimensionsUnits = "IN";
    public $weightUnits = "LBS";
    public $customerContext;
    public $shipperName;
    public $shipperPhoneNumber;
    public $shipperAddressLine1;
    public $shipperCity;
    public $shipperStateProvinceCode;
    public $shipperPostalCode;
    public $shipperCountryCode;
    public $shiptoCompanyName;
    public $shiptoAttentionName;
    public $shiptoPhoneNumber;
    public $shiptoAddressLine1;
    public $shiptoCity;
    public $shiptoStateProvinceCode;
    public $shiptoPostalCode;
    public $shiptoCountryCode;
    public $shipfromCompanyName;
    public $shipfromAttentionName;
    public $shipfromPhoneNumber;
    public $shipfromAddressLine1;
    public $shipfromCity;
    public $shipfromStateProvinceCode;
    public $shipfromPostalCode;
    public $shipfromCountryCode;
    public $serviceCode;
    public $serviceDescription;
    public $packageWeight;
    public $shipmentDigest;
    public $packagingTypeCode;
    public $packagingDescription;
    public $packagingReferenceNumberCode;
    public $packagingReferenceNumberValue;
    public $trackingNumber;
    public $shipmentIdentificationNumber;
    public $graphicImage;
    public $htmlImage;
    public $shipmentDescription;
    public $shipperAttentionName;
    public $orderPrice;

    public function setCredentials($access, $user, $pass, $shipper)
    {
        $this->AccessLicenseNumber = $access;
        $this->UserID = $user;
        $this->Password = $pass;
        $this->shipperNumber = $shipper;
        $this->credentials = 1;
        return $this->credentials;
    }

    function getShip($order_id)
    {
        if ($this->credentials != 1) {
            return array('error' => array('cod' => 1, 'message' => 'Not correct registration data'), 'success' => 0);
        }
        /* if(is_dir($filename)){} */
        $path_upsdir = Mage::getBaseDir('media') . DS . 'upslabel' . DS;
        if (!is_dir($path_upsdir)) {
            mkdir($path_upsdir, 0777);
            mkdir($path_upsdir . DS . "label" . DS, 0777);
            mkdir($path_upsdir . DS . "test_xml" . DS, 0777);
        }
        $path = Mage::getBaseDir('media') . DS . 'upslabel' . DS . "label" . DS;
        $path_xml = Mage::getBaseDir('media') . DS . 'upslabel' . DS . "test_xml" . DS;
        $this->customerContext = 'felixdolittle';
        $data = "<?xml version=\"1.0\" ?>
<AccessRequest xml:lang='en-US'>
<AccessLicenseNumber>" . $this->AccessLicenseNumber . "</AccessLicenseNumber>
<UserId>" . $this->UserID . "</UserId>
<Password>" . $this->Password . "</Password>
</AccessRequest>
<?xml version=\"1.0\"?>
<ShipmentConfirmRequest xml:lang=\"en-US\">
  <Request>
    <TransactionReference>
      <CustomerContext>" . $this->customerContext . "</CustomerContext>
      <XpciVersion/>
    </TransactionReference>
    <RequestAction>ShipConfirm</RequestAction>
    <RequestOption>validate</RequestOption>
  </Request>
  <LabelSpecification>
    <LabelPrintMethod>
      <Code>GIF</Code>
      <Description>gif file</Description>
    </LabelPrintMethod>
    <HTTPUserAgent>Mozilla/4.5</HTTPUserAgent>
    <LabelImageFormat>
      <Code>GIF</Code>
      <Description>gif</Description>
    </LabelImageFormat>
  </LabelSpecification>
  <Shipment>
   <RateInformation>
      <NegotiatedRatesIndicator/>
    </RateInformation>";
        if ($this->shipfromCountryCode != $this->shiptoCountryCode) {
            $data .= "<Description>" . $this->shipmentDescription . "</Description>";
        }
        $data .= "<Shipper>
<Name>" . $this->shipperName . "</Name>";
        $data .= "<AttentionName>" . $this->shipperAttentionName . "</AttentionName>";

        $data .= "<PhoneNumber>" . $this->shipperPhoneNumber . "</PhoneNumber>
      <ShipperNumber>" . $this->shipperNumber . "</ShipperNumber>
	  <TaxIdentificationNumber></TaxIdentificationNumber>
      <Address>
    	<AddressLine1>" . $this->shipperAddressLine1 . "</AddressLine1>
    	<City>" . $this->shipperCity . "</City>
    	<StateProvinceCode>" . $this->shipperStateProvinceCode . "</StateProvinceCode>
    	<PostalCode>" . $this->shipperPostalCode . "</PostalCode>
    	<PostcodeExtendedLow></PostcodeExtendedLow>
    	<CountryCode>" . $this->shipperCountryCode . "</CountryCode>
     </Address>
    </Shipper>
	<ShipTo>
     <CompanyName>" . $this->shiptoCompanyName . "</CompanyName>
      <AttentionName>" . $this->shiptoAttentionName . "</AttentionName>
      <PhoneNumber>" . $this->shiptoPhoneNumber . "</PhoneNumber>
      <Address>
        <AddressLine1>" . $this->shiptoAddressLine1 . "</AddressLine1>
        <City>" . $this->shiptoCity . "</City>
        <StateProvinceCode>" . $this->shiptoStateProvinceCode . "</StateProvinceCode>
        <PostalCode>" . $this->shiptoPostalCode . "</PostalCode>
        <CountryCode>" . $this->shiptoCountryCode . "</CountryCode>
      </Address>
    </ShipTo>
    <ShipFrom>
      <CompanyName>" . $this->shipfromCompanyName . "</CompanyName>
      <AttentionName>" . $this->shipfromAttentionName . "</AttentionName>
      <PhoneNumber>" . $this->shipfromPhoneNumber . "</PhoneNumber>
	  <TaxIdentificationNumber></TaxIdentificationNumber>
      <Address>
        <AddressLine1>" . $this->shipfromAddressLine1 . "</AddressLine1>
        <City>" . $this->shipfromCity . "</City>
    	<StateProvinceCode>" . $this->shipfromStateProvinceCode . "</StateProvinceCode>
    	<PostalCode>" . $this->shipfromPostalCode . "</PostalCode>
    	<CountryCode>" . $this->shipfromCountryCode . "</CountryCode>
      </Address>
    </ShipFrom>
     <PaymentInformation>
      <Prepaid>
        <BillShipper>
          <AccountNumber>" . $this->shipperNumber . "</AccountNumber>
        </BillShipper>
      </Prepaid>
    </PaymentInformation>
    <Service>
      <Code>" . $this->serviceCode . "</Code>
      <Description>" . $this->serviceDescription . "</Description>
    </Service>
    <Package>
      <PackagingType>
        <Code>" . $this->packagingTypeCode . "</Code>
      </PackagingType>
      <Description>" . $this->packagingDescription . "</Description>";
        if ( /*strlen($this->packagingReferenceNumberCode) > 0 || strlen($this->packagingReferenceNumberValue) > 0 ||*/
            $this->shiptoCountryCode == 'US' || $this->shiptoCountryCode == 'PR'
        ) {
            $data .= "<ReferenceNumber>
	  	<Code>" . $this->packagingReferenceNumberCode . "</Code>
		<Value>" . $this->packagingReferenceNumberValue . "</Value>
	  </ReferenceNumber>";
        }
        $data .= "<PackageWeight>
        <UnitOfMeasurement/>
        <Weight>" . $this->packageWeight . "</Weight>
      </PackageWeight>
      <AdditionalHandling>0</AdditionalHandling>
    </Package>
  </Shipment>
</ShipmentConfirmRequest>
";
        $file = file_put_contents($path_xml . "ShipConfirmRequest.xml", $data);
        //return $data;
        $cie = 'wwwcie';
        $testing = Mage::getStoreConfig('upslabel/testmode/testing');
        if (0 == $testing) {
            $cie = 'onlinetools';
        }
        $ch = curl_init('https://' . $cie . '.ups.com/ups.app/xml/ShipConfirm');
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $result = curl_exec($ch);
        $result = strstr($result, '<?xml');

        if ($result) {
            $file = file_put_contents($path_xml . "ShipConfirmResponse.xml", $result);
        }
        //return $result;
        $xml = simplexml_load_string($result);
        if ($xml->Response->ResponseStatusCode[0] == 1) {
            $this->shipmentDigest = $xml->ShipmentDigest[0];
            $data = "<?xml version=\"1.0\" ?>
<AccessRequest xml:lang='en-US'>
<AccessLicenseNumber>" . $this->AccessLicenseNumber . "</AccessLicenseNumber>
<UserId>" . $this->UserID . "</UserId>
<Password>" . $this->Password . "</Password>
</AccessRequest>
<?xml version=\"1.0\" ?>
<ShipmentAcceptRequest>
<Request>
<TransactionReference>
<CustomerContext>" . $this->customerContext . "</CustomerContext>
<XpciVersion>1.0001</XpciVersion>
</TransactionReference>
<RequestAction>ShipAccept</RequestAction>
</Request>
<ShipmentDigest>" . $this->shipmentDigest . "</ShipmentDigest>
</ShipmentAcceptRequest>";
            $file = file_put_contents($path_xml . "ShipAcceptRequest.xml", $data);

            $ch = curl_init('https://' . $cie . '.ups.com/ups.app/xml/ShipAccept');
            curl_setopt($ch, CURLOPT_HEADER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, 60);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            $result = curl_exec($ch);
            $result = strstr($result, '<?xml');
            if ($result) {
                $file = file_put_contents($path_xml . "ShipAcceptResponse.xml", $result);
            }
            $xml = simplexml_load_string($result);
            $this->trackingNumber = $xml->ShipmentResults[0]->PackageResults[0]->TrackingNumber[0];
            $this->shipmentIdentificationNumber = $xml->ShipmentResults[0]->ShipmentIdentificationNumber[0];
            $this->graphicImage = base64_decode($xml->ShipmentResults[0]->PackageResults[0]->LabelImage[0]->GraphicImage[0]);
            $file = fopen($path . 'label' . $this->trackingNumber . '.gif', 'w');
            fwrite($file, $this->graphicImage);
            fclose($file);
            $this->htmlImage = base64_decode($xml->ShipmentResults[0]->PackageResults[0]->LabelImage[0]->HTMLImage[0]);
            $file = file_put_contents($path . $this->trackingNumber . ".html", $this->htmlImage);
            $file = file_put_contents($path_xml . "HTML_image.html", $this->htmlImage);

            if ($this->orderPrice > 999) {
                $htmlHVReport = '<html xmlns:o="urn:schemas-microsoft-com:office:office"
xmlns:w="urn:schemas-microsoft-com:office:word"
xmlns="http://www.w3.org/TR/REC-html40">

<head>
<meta http-equiv=Content-Type content="text/html; charset=windows-1252">
<meta name=ProgId content=Word.Document>
<meta name=Generator content="Microsoft Word 11">
<meta name=Originator content="Microsoft Word 11">
<link rel=File-List href="sample%20UPS%20CONTROL%20LOG_files/filelist.xml">
<title>UPS CONTROL LOG </title>
<!--[if gte mso 9]><xml>
 <o:DocumentProperties>
  <o:Author>xlm8zff</o:Author>
  <o:LastAuthor>xlm8zff</o:LastAuthor>
  <o:Revision>2</o:Revision>
  <o:TotalTime>2</o:TotalTime>
  <o:Created>2010-09-27T12:53:00Z</o:Created>
  <o:LastSaved>2010-09-27T12:53:00Z</o:LastSaved>
  <o:Pages>1</o:Pages>
  <o:Words>116</o:Words>
  <o:Characters>662</o:Characters>
  <o:Company>UPS</o:Company>
  <o:Lines>5</o:Lines>
  <o:Paragraphs>1</o:Paragraphs>
  <o:CharactersWithSpaces>777</o:CharactersWithSpaces>
  <o:Version>11.9999</o:Version>
 </o:DocumentProperties>
</xml><![endif]--><!--[if gte mso 9]><xml>
 <w:WordDocument>
  <w:SpellingState>Clean</w:SpellingState>
  <w:GrammarState>Clean</w:GrammarState>
  <w:PunctuationKerning/>
  <w:ValidateAgainstSchemas/>
  <w:SaveIfXMLInvalid>false</w:SaveIfXMLInvalid>
  <w:IgnoreMixedContent>false</w:IgnoreMixedContent>
  <w:AlwaysShowPlaceholderText>false</w:AlwaysShowPlaceholderText>
  <w:Compatibility>
   <w:BreakWrappedTables/>
   <w:SnapToGridInCell/>
   <w:WrapTextWithPunct/>
   <w:UseAsianBreakRules/>
   <w:DontGrowAutofit/>
  </w:Compatibility>
  <w:BrowserLevel>MicrosoftInternetExplorer4</w:BrowserLevel>
 </w:WordDocument>
</xml><![endif]--><!--[if gte mso 9]><xml>
 <w:LatentStyles DefLockedState="false" LatentStyleCount="156">
 </w:LatentStyles>
</xml><![endif]-->
<style>
<!--
 /* Style Definitions */
 p.MsoNormal, li.MsoNormal, div.MsoNormal
	{mso-style-parent:"";
	margin:0in;
	margin-bottom:.0001pt;
	mso-pagination:widow-orphan;
	font-size:10.0pt;
	mso-bidi-font-size:12.0pt;
	font-family:Arial;
	mso-fareast-font-family:"Times New Roman";}
span.GramE
	{mso-style-name:"";
	mso-gram-e:yes;}
@page Section1
	{size:8.5in 11.0in;
	margin:1.0in 1.25in 1.0in 1.25in;
	mso-header-margin:.5in;
	mso-footer-margin:.5in;
	mso-paper-source:0;}
div.Section1
	{page:Section1;}
-->
</style>
<!--[if gte mso 10]>
<style>
 /* Style Definitions */
 table.MsoNormalTable
	{mso-style-name:"Table Normal";
	mso-tstyle-rowband-size:0;
	mso-tstyle-colband-size:0;
	mso-style-noshow:yes;
	mso-style-parent:"";
	mso-padding-alt:0in 5.4pt 0in 5.4pt;
	mso-para-margin:0in;
	mso-para-margin-bottom:.0001pt;
	mso-pagination:widow-orphan;
	font-size:10.0pt;
	font-family:"Times New Roman";
	mso-ansi-language:#0400;
	mso-fareast-language:#0400;
	mso-bidi-language:#0400;}
</style>
<![endif]-->
</head>
<body lang=EN-US style=\'tab-interval:.5in\'>

<div class=Section1>

<p class=MsoNormal>UPS CONTROL <span class=GramE>LOG</span></p>

<p class=MsoNormal>DATE: ' . date('d') . ' ' . date('M') . ' ' . date('Y') . ' UPS SHIPPER NO. ' . $this->shipperNumber . ' </p>
<br />
<br />
<p class=MsoNormal>TRACKING # PACKAGE ID REFRENCE NUMBER DECLARED VALUE
CURRENCY </p>
<p class=MsoNormal>--------------------------------------------------------------------------------------------------------------------------
</p>
<br /><br />
<p class=MsoNormal>' . $this->trackingNumber . ' <span class=GramE>' . $this->packagingReferenceNumberValue . ' ' . round($this->orderPrice, 2) . '</span> ' . Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol() . ' </p>
<br /><br />
<p class=MsoNormal>Total Number of Declared Value Packages = 1 </p>
<p class=MsoNormal>--------------------------------------------------------------------------------------------------------------------------
</p>
<br /><br />
<p class=MsoNormal>RECEIVED BY_________________________PICKUP
TIME__________________PKGS_______ </p>
</div>
</body>
</html>';
                file_put_contents($path . "HVR" . $this->trackingNumber . ".html", $htmlHVReport);
            }
            return array(
                'img_name' => 'label' . $this->trackingNumber . '.gif',
                'digest' => '' . $this->shipmentDigest . '',
                'trackingnumber' => '' . $this->trackingNumber . '',
                'shipidnumber' => '' . $this->shipmentIdentificationNumber . '',
            );
        } else {
            $error = '<h1>Error</h1> <ul>';
            $errorss = $xml->Response->Error[0];
            $error .= '<li>Error Severity : ' . $errorss->ErrorSeverity . '</li>';
            $error .= '<li>Error Code : ' . $errorss->ErrorCode . '</li>';
            $error .= '<li>Error Description : ' . $errorss->ErrorDescription . '</li>';
            $error .= '</ul>';
            $error .= '<textarea>' . $result . '</textarea>';
            $error .= '<textarea>' . $data . '</textarea>';
            return array('error' => $error);
            //return print_r($xml->Response->Error);
        }
    }

    function getShipFrom()
    {
        if ($this->credentials != 1) {
            return array('error' => array('cod' => 1, 'message' => 'Not correct registration data'), 'success' => 0);
        }
        /* if(is_dir($filename)){} */
        $path_upsdir = Mage::getBaseDir('media') . DS . 'upslabel' . DS;
        if (!is_dir($path_upsdir)) {
            mkdir($path_upsdir, 0777);
            mkdir($path_upsdir . DS . "label" . DS, 0777);
            mkdir($path_upsdir . DS . "test_xml" . DS, 0777);
        }
        $path = Mage::getBaseDir('media') . DS . 'upslabel' . DS . "label" . DS;
        $path_xml = Mage::getBaseDir('media') . DS . 'upslabel' . DS . "test_xml" . DS;
        $this->customerContext = 'felixdolittle';
        $data = "<?xml version=\"1.0\" ?>
        <AccessRequest xml:lang='en-US'>
        <AccessLicenseNumber>" . $this->AccessLicenseNumber . "</AccessLicenseNumber>
        <UserId>" . $this->UserID . "</UserId>
        <Password>" . $this->Password . "</Password>
        </AccessRequest>
        <?xml version=\"1.0\"?>
        <ShipmentConfirmRequest xml:lang=\"en-US\">
          <Request>
            <TransactionReference>
              <CustomerContext>" . $this->customerContext . "</CustomerContext>
              <XpciVersion/>
            </TransactionReference>
            <RequestAction>ShipConfirm</RequestAction>
            <RequestOption>validate</RequestOption>
          </Request>
          <LabelSpecification>
            <LabelPrintMethod>
              <Code>GIF</Code>
              <Description>gif file</Description>
            </LabelPrintMethod>
            <HTTPUserAgent>Mozilla/4.5</HTTPUserAgent>
            <LabelImageFormat>
              <Code>GIF</Code>
              <Description>gif</Description>
            </LabelImageFormat>
          </LabelSpecification>
          <Shipment>
           <RateInformation>
              <NegotiatedRatesIndicator/>
            </RateInformation>";
        if ($this->shipfromCountryCode != $this->shiptoCountryCode) {
            $data .= "<Description>" . $this->shipmentDescription . "</Description>";
        }
        $data .= "<Shipper>
        <Name>" . $this->shipperName . "</Name>";
        $data .= "<AttentionName>" . $this->shipperAttentionName . "</AttentionName>";

        $data .= "<PhoneNumber>" . $this->shipperPhoneNumber . "</PhoneNumber>
              <ShipperNumber>" . $this->shipperNumber . "</ShipperNumber>
        	  <TaxIdentificationNumber></TaxIdentificationNumber>
              <Address>
            	<AddressLine1>" . $this->shipperAddressLine1 . "</AddressLine1>
            	<City>" . $this->shipperCity . "</City>
            	<StateProvinceCode>" . $this->shipperStateProvinceCode . "</StateProvinceCode>
            	<PostalCode>" . $this->shipperPostalCode . "</PostalCode>
            	<PostcodeExtendedLow></PostcodeExtendedLow>
            	<CountryCode>" . $this->shipperCountryCode . "</CountryCode>
             </Address>
            </Shipper>
        	<ShipFrom>
             <CompanyName>" . $this->shiptoCompanyName . "</CompanyName>
              <AttentionName>" . $this->shiptoAttentionName . "</AttentionName>
              <PhoneNumber>" . $this->shiptoPhoneNumber . "</PhoneNumber>
              <TaxIdentificationNumber></TaxIdentificationNumber>
              <Address>
                <AddressLine1>" . $this->shiptoAddressLine1 . "</AddressLine1>
                <City>" . $this->shiptoCity . "</City>
                <StateProvinceCode>" . $this->shiptoStateProvinceCode . "</StateProvinceCode>
                <PostalCode>" . $this->shiptoPostalCode . "</PostalCode>
                <CountryCode>" . $this->shiptoCountryCode . "</CountryCode>
              </Address>
            </ShipFrom>
            <ShipTo>
              <CompanyName>" . $this->shipfromCompanyName . "</CompanyName>
              <AttentionName>" . $this->shipfromAttentionName . "</AttentionName>
              <PhoneNumber>" . $this->shipfromPhoneNumber . "</PhoneNumber>
              <Address>
                <AddressLine1>" . $this->shipfromAddressLine1 . "</AddressLine1>
                <City>" . $this->shipfromCity . "</City>
            	<StateProvinceCode>" . $this->shipfromStateProvinceCode . "</StateProvinceCode>
            	<PostalCode>" . $this->shipfromPostalCode . "</PostalCode>
            	<CountryCode>" . $this->shipfromCountryCode . "</CountryCode>
              </Address>
            </ShipTo>
             <PaymentInformation>
              <Prepaid>
                <BillShipper>
                  <AccountNumber>" . $this->shipperNumber . "</AccountNumber>
                </BillShipper>
              </Prepaid>
            </PaymentInformation>
            <Service>
              <Code>" . $this->serviceCode . "</Code>
              <Description>" . $this->serviceDescription . "</Description>
            </Service>
            <Package>
              <PackagingType>
                <Code>" . $this->packagingTypeCode . "</Code>
              </PackagingType>
              <Description>" . $this->packagingDescription . "</Description>";
        if ( /*strlen($this->packagingReferenceNumberCode) > 0 || strlen($this->packagingReferenceNumberValue) > 0 ||*/
            $this->shiptoCountryCode == 'US' || $this->shiptoCountryCode == 'PR'
        ) {
            $data .= "<ReferenceNumber>
        	  	<Code>" . $this->packagingReferenceNumberCode . "</Code>
        		<Value>" . $this->packagingReferenceNumberValue . "</Value>
        	  </ReferenceNumber>";
        }
        $data .= "<PackageWeight>
                <UnitOfMeasurement/>
                <Weight>" . $this->packageWeight . "</Weight>
              </PackageWeight>
              <AdditionalHandling>0</AdditionalHandling>
            </Package>
          </Shipment>
        </ShipmentConfirmRequest>
        ";
        $file = file_put_contents($path_xml . "ShipConfirmRequest.xml", $data);
        //return $data;
        $cie = 'wwwcie';
        $testing = Mage::getStoreConfig('upslabel/testmode/testing');
        if (0 == $testing) {
            $cie = 'onlinetools';
        }
        $ch = curl_init('https://' . $cie . '.ups.com/ups.app/xml/ShipConfirm');
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $result = curl_exec($ch);
        $result = strstr($result, '<?xml');

        if ($result) {
            $file = file_put_contents($path_xml . "ShipConfirmResponse.xml", $result);
        }
        //return $result;
        $xml = simplexml_load_string($result);
        if ($xml->Response->ResponseStatusCode[0] == 1) {
            $this->shipmentDigest = $xml->ShipmentDigest[0];
            $data = "<?xml version=\"1.0\" ?>
        <AccessRequest xml:lang='en-US'>
        <AccessLicenseNumber>" . $this->AccessLicenseNumber . "</AccessLicenseNumber>
        <UserId>" . $this->UserID . "</UserId>
        <Password>" . $this->Password . "</Password>
        </AccessRequest>
        <?xml version=\"1.0\" ?>
        <ShipmentAcceptRequest>
        <Request>
        <TransactionReference>
        <CustomerContext>" . $this->customerContext . "</CustomerContext>
        <XpciVersion>1.0001</XpciVersion>
        </TransactionReference>
        <RequestAction>ShipAccept</RequestAction>
        </Request>
        <ShipmentDigest>" . $this->shipmentDigest . "</ShipmentDigest>
        </ShipmentAcceptRequest>";
            $file = file_put_contents($path_xml . "ShipAcceptRequest.xml", $data);

            $ch = curl_init('https://' . $cie . '.ups.com/ups.app/xml/ShipAccept');
            curl_setopt($ch, CURLOPT_HEADER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, 60);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            $result = curl_exec($ch);
            $result = strstr($result, '<?xml');
            if ($result) {
                $file = file_put_contents($path_xml . "ShipAcceptResponse.xml", $result);
            }
            $xml = simplexml_load_string($result);
            $this->trackingNumber = $xml->ShipmentResults[0]->PackageResults[0]->TrackingNumber[0];
            $this->shipmentIdentificationNumber = $xml->ShipmentResults[0]->ShipmentIdentificationNumber[0];
            $this->graphicImage = base64_decode($xml->ShipmentResults[0]->PackageResults[0]->LabelImage[0]->GraphicImage[0]);
            $file = fopen($path . 'label' . $this->trackingNumber . '.gif', 'w');
            fwrite($file, $this->graphicImage);
            fclose($file);
            $this->htmlImage = base64_decode($xml->ShipmentResults[0]->PackageResults[0]->LabelImage[0]->HTMLImage[0]);
            $file = file_put_contents($path . $this->trackingNumber . ".html", $this->htmlImage);
            $file = file_put_contents($path_xml . "HTML_image.html", $this->htmlImage);

            if ($this->orderPrice > 999) {
                $htmlHVReport = '<html xmlns:o="urn:schemas-microsoft-com:office:office"
        xmlns:w="urn:schemas-microsoft-com:office:word"
        xmlns="http://www.w3.org/TR/REC-html40">

        <head>
        <meta http-equiv=Content-Type content="text/html; charset=windows-1252">
        <meta name=ProgId content=Word.Document>
        <meta name=Generator content="Microsoft Word 11">
        <meta name=Originator content="Microsoft Word 11">
        <link rel=File-List href="sample%20UPS%20CONTROL%20LOG_files/filelist.xml">
        <title>UPS CONTROL LOG </title>
        <!--[if gte mso 9]><xml>
         <o:DocumentProperties>
          <o:Author>xlm8zff</o:Author>
          <o:LastAuthor>xlm8zff</o:LastAuthor>
          <o:Revision>2</o:Revision>
          <o:TotalTime>2</o:TotalTime>
          <o:Created>2010-09-27T12:53:00Z</o:Created>
          <o:LastSaved>2010-09-27T12:53:00Z</o:LastSaved>
          <o:Pages>1</o:Pages>
          <o:Words>116</o:Words>
          <o:Characters>662</o:Characters>
          <o:Company>UPS</o:Company>
          <o:Lines>5</o:Lines>
          <o:Paragraphs>1</o:Paragraphs>
          <o:CharactersWithSpaces>777</o:CharactersWithSpaces>
          <o:Version>11.9999</o:Version>
         </o:DocumentProperties>
        </xml><![endif]--><!--[if gte mso 9]><xml>
         <w:WordDocument>
          <w:SpellingState>Clean</w:SpellingState>
          <w:GrammarState>Clean</w:GrammarState>
          <w:PunctuationKerning/>
          <w:ValidateAgainstSchemas/>
          <w:SaveIfXMLInvalid>false</w:SaveIfXMLInvalid>
          <w:IgnoreMixedContent>false</w:IgnoreMixedContent>
          <w:AlwaysShowPlaceholderText>false</w:AlwaysShowPlaceholderText>
          <w:Compatibility>
           <w:BreakWrappedTables/>
           <w:SnapToGridInCell/>
           <w:WrapTextWithPunct/>
           <w:UseAsianBreakRules/>
           <w:DontGrowAutofit/>
          </w:Compatibility>
          <w:BrowserLevel>MicrosoftInternetExplorer4</w:BrowserLevel>
         </w:WordDocument>
        </xml><![endif]--><!--[if gte mso 9]><xml>
         <w:LatentStyles DefLockedState="false" LatentStyleCount="156">
         </w:LatentStyles>
        </xml><![endif]-->
        <style>
        <!--
         /* Style Definitions */
         p.MsoNormal, li.MsoNormal, div.MsoNormal
        	{mso-style-parent:"";
        	margin:0in;
        	margin-bottom:.0001pt;
        	mso-pagination:widow-orphan;
        	font-size:10.0pt;
        	mso-bidi-font-size:12.0pt;
        	font-family:Arial;
        	mso-fareast-font-family:"Times New Roman";}
        span.GramE
        	{mso-style-name:"";
        	mso-gram-e:yes;}
        @page Section1
        	{size:8.5in 11.0in;
        	margin:1.0in 1.25in 1.0in 1.25in;
        	mso-header-margin:.5in;
        	mso-footer-margin:.5in;
        	mso-paper-source:0;}
        div.Section1
        	{page:Section1;}
        -->
        </style>
        <!--[if gte mso 10]>
        <style>
         /* Style Definitions */
         table.MsoNormalTable
        	{mso-style-name:"Table Normal";
        	mso-tstyle-rowband-size:0;
        	mso-tstyle-colband-size:0;
        	mso-style-noshow:yes;
        	mso-style-parent:"";
        	mso-padding-alt:0in 5.4pt 0in 5.4pt;
        	mso-para-margin:0in;
        	mso-para-margin-bottom:.0001pt;
        	mso-pagination:widow-orphan;
        	font-size:10.0pt;
        	font-family:"Times New Roman";
        	mso-ansi-language:#0400;
        	mso-fareast-language:#0400;
        	mso-bidi-language:#0400;}
        </style>
        <![endif]-->
        </head>
        <body lang=EN-US style=\'tab-interval:.5in\'>

        <div class=Section1>

        <p class=MsoNormal>UPS CONTROL <span class=GramE>LOG</span></p>

        <p class=MsoNormal>DATE: ' . date('d') . ' ' . date('M') . ' ' . date('Y') . ' UPS SHIPPER NO. ' . $this->shipperNumber . ' </p>
        <br />
        <br />
        <p class=MsoNormal>TRACKING # PACKAGE ID REFRENCE NUMBER DECLARED VALUE
        CURRENCY </p>
        <p class=MsoNormal>--------------------------------------------------------------------------------------------------------------------------
        </p>
        <br /><br />
        <p class=MsoNormal>' . $this->trackingNumber . ' <span class=GramE>' . $this->packagingReferenceNumberValue . ' ' . round($this->orderPrice, 2) . '</span> ' . Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol() . ' </p>
        <br /><br />
        <p class=MsoNormal>Total Number of Declared Value Packages = 1 </p>
        <p class=MsoNormal>--------------------------------------------------------------------------------------------------------------------------
        </p>
        <br /><br />
        <p class=MsoNormal>RECEIVED BY_________________________PICKUP
        TIME__________________PKGS_______ </p>
        </div>
        </body>
        </html>';
                file_put_contents($path . "HVR" . $this->trackingNumber . ".html", $htmlHVReport);
            }
            return array(
                'img_name' => 'label' . $this->trackingNumber . '.gif',
                'digest' => '' . $this->shipmentDigest . '',
                'trackingnumber' => '' . $this->trackingNumber . '',
                'shipidnumber' => '' . $this->shipmentIdentificationNumber . '',
            );
        } else {
            $error = '<h1>Error</h1> <ul>';
            $errorss = $xml->Response->Error[0];
            $error .= '<li>Error Severity : ' . $errorss->ErrorSeverity . '</li>';
            $error .= '<li>Error Code : ' . $errorss->ErrorCode . '</li>';
            $error .= '<li>Error Description : ' . $errorss->ErrorDescription . '</li>';
            $error .= '</ul>';
            $error .= '<textarea>' . $result . '</textarea>';
            $error .= '<textarea>' . $data . '</textarea>';
            return array('error' => $error);
            //return print_r($xml->Response->Error);
        }
    }

    public function deleteLabel($trnum)
    {
        $path_xml = Mage::getBaseDir('media') . DS . 'upslabel' . DS . "test_xml" . DS;
        $cie = 'wwwcie';
        $testing = Mage::getStoreConfig('upslabel/testmode/testing');
        $shipIndefNumbr = $trnum;
        if (0 == $testing) {
            $cie = 'onlinetools';
        }
        else {
            /*$trnum = '1Z2220060291994175';*/
            $shipIndefNumbr = '1ZISDE016691676846';
        }
        $data = "<?xml version=\"1.0\" ?>
<AccessRequest xml:lang='en-US'>
<AccessLicenseNumber>" . $this->AccessLicenseNumber . "</AccessLicenseNumber>
<UserId>" . $this->UserID . "</UserId>
<Password>" . $this->Password . "</Password>
</AccessRequest>
<?xml version=\"1.0\" ?>
<VoidShipmentRequest>
<Request>
<RequestAction>1</RequestAction>
</Request>
<ShipmentIdentificationNumber>" . $shipIndefNumbr . "</ShipmentIdentificationNumber>
    <ExpandedVoidShipment>
          <ShipmentIdentificationNumber>" . $shipIndefNumbr . "</ShipmentIdentificationNumber>
          </ExpandedVoidShipment>
</VoidShipmentRequest> ";
        /*<TrackingNumber>" . $trnum . "</TrackingNumber>*/
        /*  */
        $file = file_put_contents($path_xml . "VoidShipmentRequest.xml", $data);

        $ch = curl_init('https://' . $cie . '.ups.com/ups.app/xml/Void');
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $result = curl_exec($ch);
        $result = strstr($result, '<?xml');
        if ($result) {
            $file = file_put_contents($path_xml . "VoidShipmentResponse.xml", $result);
        }
        $xml = simplexml_load_string($result);
        if ($xml->Response->Error[0]) {
            $error = '<h1>Error</h1> <ul>';
            $errorss = $xml->Response->Error[0];
            $error .= '<li>Error Severity : ' . $errorss->ErrorSeverity . '</li>';
            $error .= '<li>Error Code : ' . $errorss->ErrorCode . '</li>';
            $error .= '<li>Error Description : ' . $errorss->ErrorDescription . '</li>';
            $error .= '</ul>';
            $error .= '<textarea>' . $result . '</textarea>';
            $error .= '<textarea>' . $data . '</textarea>';
            return array('error' => $error);
        } else {
            return true;
        }
    }

}

?>
