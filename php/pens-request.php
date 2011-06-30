<?php

/* Software License Agreement (BSD License)
 * 
 * Copyright (c) 2010-2011, Rustici Software, LLC
 * All rights reserved.
 * 
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *     * Neither the name of the <organization> nor the
 *       names of its contributors may be used to endorse or promote products
 *       derived from this software without specific prior written permission.
 * 
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONtrIBUTORS "AS IS" AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL Rustici Software, LLC BE LIABLE FOR ANY
 * DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
 * ON ANY THEORY OF LIABILITY, WHETHER IN CONtrACT, StrICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

/* The code below shows an example of how one might use server side code to create
   a PENS request. The PensRequest class is a simple object based wrapper around an HTTP
   based PENS request. All other code below it shows an example of how you can use the class. */

class PensRequest {
    public $clientNameSpace = null;
    public $targetSystemUrl = null;
    public $pensVersion = null;
    public $command = 'collect';
    public $packageType = null;
    public $packageTypeVersion = null;
    public $packageFormat = null;
    public $packageId = null;
    public $packageUrl = null;
    public $packageUrlUserId = null;
    public $packageUrlAccount = null;
    public $packageUrlPassword = null;
    public $packageUrlExpiry = null;
    public $client = null;
    public $systemUserId = null;
    public $systemPassword = null;
    public $receipt = null;
    public $alerts = null;
    public $vendorData = null;

    public $pensParameterMap = array(
        'targetSystemUrl' => 'target-system-url',
        'pensVersion' => 'pens-version',
        'command' => 'command',
        'packageType' => 'package-type',
        'packageTypeVersion' => 'package-type-version',
        'packageFormat' => 'package-format',
        'packageId' => 'package-id',
        'packageUrl' => 'package-url',
        'packageUrlUserId' => 'package-url-user-id',
        'packageUrlAccount' => 'package-url-account',
        'packageUrlPassword' => 'package-url-password',
        'packageUrlExpiry' => 'package-url-expiry',
        'client' => 'client',
        'systemUserId' => 'system-user-id',
        'systemPassword' => 'system-password',
        'receipt' => 'receipt',
        'alerts' => 'alerts',
        'vendorData' => 'vendor-data'
    );

    public function getCommandParameters(){
        $paramStr = '';
        foreach ($this->pensParameterMap as $member => $paramName){
            if(!empty($this->$member)){
                $paramVal = $this->$member;
                $paramStr .= $paramName . '=' . urlencode($paramVal) . '&';
            }
        }
        $paramStr = substr($paramStr, 0, strlen($paramStr) - 1);
        return $paramStr;
    }

    public function getCommandUrl(){
        return $this->targetSystemUrl . '?' . $this->getCommandParameters();
    }
}





//--------- End PENS request class, begin page processing ------

function fillPensRequest($postParams, $pensRequest){
    $paramMap = array_flip($pensRequest->pensParameterMap);
    foreach($_POST as $postParamName => $postParamVal) {
        if($postParamName == 'URL'){
            $pensRequest->targetSystemUrl = $postParamVal;
        } else {
            if(isset($paramMap[$postParamName])){
                $memberName = $paramMap[$postParamName];
                $pensRequest->$memberName = $postParamVal;
            }
        }
    }
}

$formPosted = !empty($_POST);

$pensRequest = new PensRequest();
$commandUrl = '';

if ($formPosted) {
    fillPensRequest($_POST, $pensRequest);
    $commandUrl = $pensRequest->getCommandUrl(); 
}


//For package url expiry
$oneWeek = 60 * 60 * 24 * 7;
$oneWeekFromNow = time() + $oneWeek;
$dateStr = gmdate('Y-m-d\TH:i:s\Z', $oneWeekFromNow);


?>
<html>
<head>
    <title>Pens Example Code</title>
    <style type="text/css"> 
            body { margin:0; padding:20px; color:#444444; }
            #top,#bottom,#left,#right { position:fixed; background:#DFE5EA; }
            #top,#bottom { left:0; width:100%; height:16px; }
            #top { top:0; }
            #bottom { bottom:0; }
            #left,#right { top:0; height:100%; width:16px; }
            #left { left:0; }
            #right { right:0; }
            #content { text-align:center; width:100%; 
                       font-family:'Lucida Grande','Helvetica Neue',Helvetica,Arial,sans-serif;}
            #content p { font-size:1.2em; width:70%; margin:auto; }
            
            #pensFormTable { margin-top: 50px; margin-left:auto; margin-right:auto; }
            #pensFormTable tr td { padding-right: 20px; }
            tr.mandatory td { font-weight: bold; }
            span.label {font-weight: bold; }
        </style>
        <!--[if lte IE 6]>
        <style media="screen">
            body { border: solid #DFE5EA 16px; }
            #left,#right,#top,#bottom { display: none; }
        <![endif]-->
</head>
<body>
    <div id="content">
                  <h1>Example PENS Code - Simple Frontend</h1>

        <?php if ($formPosted) { ?>
        
               <p><span class="label">Collect URL: </span>
               <a href="<?php echo $commandUrl ?>"><?php echo $commandUrl ?></a></p>
        
        <?php } else { ?>

                  <p align="left" class="style5">This is a tool to generate sample PENS commands for development and debugging of PENS publishers and PENS processor servers. This frontend is a derivation of the page found at http://pens.lmstesting.com/test/pens-test.html , altered to serve as just a simple frontend for underlying "backend" code to create and execute a PENS collect request, to server as a sample for content authoring tool developers that want to implement PENS publishing.
                  Please do not consider it a user interface example.<br/><br/>To create a sample command string,
                  complete all the <b>boldface</b> fields below <em>and</em> any desired optional (non-boldface) fields and submit the form, which will use the example PensRequest class to create a PENS collect URL. To learn more about each PENS parameter, just hover your cursor over the parameter name below.</p>

        
            <table id="pensFormTable"> 
                <tr> 
                <form METHOD=POST ACTION="<?php echo $_SERVER['PHP_SELF'] ?>"> 
                <tr border="2" title="URL associated with the product or the service generating the package-id" class="mandatory"> 
                    <td>client namespace URL</td> 
                    <td><input type="text" name="namespace" size="50" class="mandatory" value="http://yoursite.com/"></td> 
                </tr> 
                <tr border="2" title="URL of target system that will perform processing." class="mandatory"> 
                    <td>target system URL</td> 
                    <td><input type="text" name="URL" size="50" class="mandatory" value="http://cloud.scorm.com/ScormEngineInterface/PENS.jsp"></td> 
                </tr> 
                <tr> 
                    <td colspan="2"><hr /></td> 
                </tr> 
                 
                <input type="hidden" name="command" value="collect"> 
                <tr title="Version of Package Exchange Notification Service protocol used by client submitting the package." class="mandatory"> 
                    <td>pens-version</td> 
                    <td width="50em"><input type="text" name="pens-version" ID="pens-version" size="50" class="mandatory" value="1.0.0" > 
                </tr> 
                <tr title="Type of content package being sent." class="mandatory"> 
                    <td>package-type</td> 
                    <td>    <select name="package-type" id="package-type" class="mandatory" onChange="SetPackageVersion(this);"> 
                      <option value="aicc-pkg" selected="true">aicc-pkg</option> 
                      <option value="scorm-pif">scorm-pif</option> 
                      <option value="ims-qti">ims-qti</option> 
                    </select></td> 
                </tr> 
                <tr title="Version of the packaging specification relevant for the package to be processed." class="mandatory"> 
                    <td>package-type-version</td>
                    <td><input type="text" name="package-type-version" id="package-type-version" size="50" class="mandatory" value="1.0"></td> 
                </tr> 
                <tr title="Package archive format." class="mandatory"> 
                    <td>package-format</td> 
                    <td><input name="package-format" id="package-format" type="text" class="mandatory" value="zip" size="50"></td> 
                </tr> 
                <tr title="Unique identifier for package; a URI consisting a globally unique namespace taken from the URL associated with the product or the service generating the ID, plus an ID unique within the service itself." class="mandatory"> 
                    <td>package-id</td> 
                    <td><input type="text" name="package-id" id="package-id" size="50" value="http://yoursite.com:your-pens-package-id" class="mandatory"></td> 
                </tr> 
                <tr title="Location of package archive ready for transfer/action." class="mandatory"> 
                    <td>package-url</td> 
                    <td><input type="text" name="package-url" size="50" class="mandatory" value="http://your-staging-server.com/test-course.zip"></td> 
                </tr> 
                <tr title="User id required for system to retrieve package from URL (optional)." class="optional"> 
                    <td>package-url-user-id</td> 
                    <td><input type="text" name="package-url-user-id" size="50" class="optional"></td> 
                </tr> 
                <tr title="Account required for system to retrieve package from URL (optional)." class="optional"> 
                    <td>package-url-account</td> 
                    <td><input type="text" name="package-url-account" size="50" class="optional"></td> 
                </tr> 
                <tr title="Password required for system to retrieve package from URL (optional)" class="optional"> 
                    <td>package-url-password</td> 
                    <td><input type="text" name="package-url-password" size="50" class="optional"></td> 
                </tr> 
                <tr title="The package is expected to be available for processing until at least the date and time specified." class="mandatory"> 
                    <td>package-url-expiry</td> 
                    <td><input type="text" name="package-url-expiry"  id="package-url-expiry" size="50" value="<?php echo $dateStr ?>" class="mandatory"></td> 
                </tr> 
                <tr title="Name or ID for client submitting the content package to the target system." class="mandatory"> 
                    <td>client</td> 
                    <td><input type="text" name="client" size="50" class="mandatory" value="test-harness-v1.0.3"></td> 
                </tr> 
                <tr title="User id or sign-on for target system (optional)." class="optional"> 
                    <td>system-user-id</td> 
                    <td><input type="text" name="system-user-id" size="50" class="optional"></td> 
                </tr> 
                <tr title="Password for target system (optional)." class="optional"> 
                    <td>system-password</td> 
                    <td><input type="text" name="system-password" size="50" class="optional"></td> 
                </tr> 
                <tr title="URL to send acknowledgement receipt after collecting a package." class="mandatory"> 
                    <td>receipt</td> 
                    <td><input type="text" name="receipt" id="receipt" size="50" class="mandatory" value="mailto:youremail@example.com (or http://yourdomain.com/pens-response-listener.php)"></td> 
                </tr> 
                <tr title="URL to send alerts to while processing the package (optional)." class="optional"> 
                    <td>alerts</td> 
                    <td><input type="text" name="alerts" size="50" class="optional" value="mailto:youremail@example.com (or http://yourdomain.com/pens-response-listener.php)"></td> 
                </tr> 
                <tr title="Character string that may be used to transfer vendor-specific data such as processing hints or deployment information (optional)." class="optional"> 
                    <td>vendor-data<br/> 
                    <td><TEXTAREA ROWS="5" COLS="40" name="vendor-data" class="optional" WRAP="virtual"></TEXTAREA></td> 
                </tr> 
                <tr> 
                    <td colspan="2"><hr /></td> 
                </tr> 
                <tr> 
                    <td colspan="2" align="center"> 
                    <input type="submit" name="submit" value="Create URL" />
                    </td> 
                </tr> 
                <tr> 
                    <td colspan="2"><div id="commandURL" class="commandURL" ></div></td> 
                </tr> 
                </form> 
            </table>
        <?php } ?>
    </div>
    <div id="top"></div>
    <div id="bottom"></div>
    <div id="left"></div>
    <div id="right"></div>
</body>
</html>
