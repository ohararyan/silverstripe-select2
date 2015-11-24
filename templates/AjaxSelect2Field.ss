<% if $isReadonly %>
	<span id="$ID"<% if $extraClass %> class="$extraClass"<% end_if %>>
		$Value
	</span>
<% else %>
	<select $AttributesHTML>
		<% loop $Options %>
	    	<option value="$Value.XML"<% if $Selected %> selected="selected"<% end_if %><% if $Disabled %> disabled="disabled"<% end_if %>>$Title.XML</option>
	    <% end_loop %>
	</select>
<% end_if %>
<a hre="#" class="ajaxselect2Remove"<% if not Value %>style="display:none;"<% end_if %>>Clear</a>