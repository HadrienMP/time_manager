<?php
$email = array(
	'name'	=> 'email',
	'id'	=> 'email',
	'value'	=> set_value('email'),
	'maxlength'	=> 80,
	'size'	=> 30,
);
?>
<?php echo form_open($this->uri->uri_string()); ?>
<table>
	<tr>
		<td><?php echo form_label($this->lang->line('Email'), $email['id']); ?></td>
		<td class="<?php echo form_error($email['name']) != "" || isset($errors[$email['name']]) ? "wrong" : "" ?>">
            <?php echo form_input($email); ?>
        </td>
	</tr>
</table>
<?php echo form_submit('send', $this->lang->line('Send')); ?>
<?php echo form_close(); ?>