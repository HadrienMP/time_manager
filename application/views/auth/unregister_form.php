<?php
$password = array(
	'name'	=> 'password',
	'id'	=> 'password',
	'size'	=> 30,
);
?>
<?php echo form_open($this->uri->uri_string()); ?>
<table>
	<tr>
		<td><?php echo form_label($this->lang->line('Password'), $password['id']); ?></td>
		<td class="<?php echo form_error($password['name']) != "" || isset($errors[$password['name']]) ? "wrong" : "" ?>">
            <?php echo form_password($password); ?>
        </td>
	</tr>
</table>
<?php echo form_submit('cancel', $this->lang->line('Delete account')); ?>
<?php echo form_close(); ?>