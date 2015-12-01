<input id="$ID" type="hidden" name="$Name.ATT" value="$Value.ATT" />

<select $AttributesHTML>
	<% loop $Options %>
    	<option value="$Value.XML"<% if $Selected %> selected="selected"<% end_if %><% if $Disabled %> disabled="disabled"<% end_if %>>$Title.XML</option>
    <% end_loop %>
</select>