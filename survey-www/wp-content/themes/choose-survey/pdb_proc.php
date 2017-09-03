<?php

function data_uri($file, $mime) {
    return "data:$mime;base64," . base64_encode(file_get_contents($file));
}

// JJA Implemented

function output_jpg($file)
{
  $Img = imagecreatefromjpeg($file);
  header('Content-Type: image/jpg');
  // output jpg
  imagejpeg($Img);
  imagedestroy($Img);
}

// Operation type HTTP-POST parameter: dihedrals, seq, ram
$OpType = htmlspecialchars($_POST["op"]);

$allowedExts = array("pdb", "ent");
$temp = explode(".", $_FILES["file"]["name"]);
$extension = end($temp);
if (
   //($_FILES["file"]["type"] == "text/plain")
      ($_FILES["file"]["size"] < 4000000)
   && in_array($extension, $allowedExts)
)
  {
  if ($_FILES["file"]["error"] > 0)
    {
    echo "Error: " . $_FILES["file"]["error"] . "<br>";
    }
  else
    {
	$FilePath = $_FILES["file"]["tmp_name"];	
	$FileName = $_FILES["file"]["name"];
    echo "Upload: " . $FileName . "<br>";
    //echo "Type: " . $_FILES["file"]["type"] . "<br>";
    echo "Size: " . ($_FILES["file"]["size"] / 1024) . " kB<br>";
    //echo "Stored in: " . $FilePath + "<br>";
	
	echo "<br>";	
	echo "<img src='./msg-info.jpg'> <b>Computation output:</b> <br><br>";
	
	if ( ($OpType == "dihedrals") )
	{	
		//$dih = shell_exec("/data/httpd/home/mxba001/dihedrals-64 " + $FilePath);
		$dih = shell_exec("./dihedrals-64 " . $FilePath);
		$output = str_replace("\n", "<br>\n", $dih);	
		// For security, mask file path
		$output = str_replace($FilePath, $FileName, $output);	
		echo $output;
	}
	
	if ( ($OpType == "seq") )
	{	
		$seq = shell_exec("./pdb-seq-64 " . $FilePath);
		$output = str_replace("\n", "<br>", $seq);
		$output = str_replace(";", "<br>\n", $output);
		// For security, mask file path
		$output = str_replace($FilePath, $FileName, $output);	
		echo $output;
	}

        if ( ($OpType == "ram") )
	{	
		$ram = shell_exec("./ramplot-64 " . $FilePath . " /jpg");
		$output = str_replace("\n", "<br>\n", $ram);
		
		$ImgFilePath = str_replace('/tmp/', './_tmp/', $FilePath) . '.jpg';

		//echo $ImgFilePath . "<br>\n";
		//echo "size: " . filesize($ImgFilePath);

		echo "(please wait if graph hasn't yet appeared)<br>";
		
		echo "<img alt='Ramachandran plot of " . $FileName . "' src='" . data_uri($ImgFilePath, 'image/jpeg') . "'>";
		//echo "<img alt='Ramachandran plot of " . $FileName . "' src='" . output_jpg($ImgFilePath) . "'>";


	}
	
    }
  }
else
  {
	if ($_FILES["file"]["size"] > 4000000)
	{
	  echo "<img src='./msg-error.jpg'> <b>File too large (> 4Mb)</b><br>";
	} else
	{
	  echo "<img src='./msg-error.jpg'> <b>Invalid file submitted</b><br>";
	  echo "<br>The file should meet the following requirements:";
	  echo "<ul>";
	  echo "<li>must be of .pdb or .ent file extension</li>";
	  echo "<li>must not exceed ~4mb</li>";
	  echo "</ul>";
	  echo "<b>Please use the 'back' button on your browser to select another file!</b>";
	}
  }
?> 
</font>
<br>
                  </font>
                </td>
              </tr>
              <tr>
                <td width="100%" height="21">
                  <p><br>
                </td>
              </tr>
            </table>
          </div>
          </td>
        </tr>
      </table>
    </div>
  </td>
</tr>
</table>

</body>

</html>
