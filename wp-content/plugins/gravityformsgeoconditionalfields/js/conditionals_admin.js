
if(window.gform) {

	gform.addFilter('gform_conditional_logic_operators', function (operators, objectType, fieldId) {
		if(fieldId != 0) {
			var field = GetFieldById(fieldId);
			if(field.type == 'geo_country_field' || field.type == 'geo_continent_field') {
				operators = {is: "is", isnot: "isNot"};
			}
		}
		return operators;
	});
	gform.addFilter("gform_conditional_logic_values_input", function(str, objectType, ruleIndex, selectedFieldId, selectedValue) {
		var field = GetFieldById(selectedFieldId);
		if(field.type == 'geo_country_field' || field.type == 'geo_continent_field') {
			str = GetRuleValuesDropDown(field.choices, objectType, ruleIndex, selectedValue, false);
		}
		return str;
	}, 10, 5);

	gform.addFilter("gform_merge_tags", function(mergeTags, elementId, hideAllFields, excludeFieldTypes, isPrepop, option) {
		mergeTags["custom"].tags.push({ tag: '{geo_country}', label: gfgcf_localisations.country_code });
		mergeTags["custom"].tags.push({ tag: '{geo_country_display}', label: gfgcf_localisations.country_display });
		mergeTags["custom"].tags.push({ tag: '{geo_continent}', label: gfgcf_localisations.continent_code });
		mergeTags["custom"].tags.push({ tag: '{geo_continent_display}', label: gfgcf_localisations.continent_display });
		return mergeTags;
	});
}