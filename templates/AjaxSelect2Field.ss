<% if $isReadonly %>
	<span id="$ID"<% if $extraClass %> class="$extraClass"<% end_if %>>
		$Value
	</span>
<% else %>
	<input $AttributesHTML />
<% end_if %>
<a hre="#" class="ajaxselect2Remove"<% if not Value %>style="display:none;"<% end_if %>>Clear</a>