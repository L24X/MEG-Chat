<?
$message=exec("sudo -u root -S bash /var/www/html/MEG-Chat/update.sh < /home/tilo2/password.txt", $output, $return_var);
echo $outpu;
echo "<br>";
echo $return_var;
echo "<br>";
echo $message;

?>
<p style="color: green; ">Erfolgreich gespeichert!</p>
