SELECT t.term_id, @parent := t.parent AS parent_id, tm1.meta_value AS metadata1, tm2.meta_value AS metadata2
FROM (
	SELECT *
	FROM wp_term_taxonomy AS tt
	ORDER BY
		CASE
			WHEN tt.term_id > tt.parent THEN tt.term_id
			ELSE tt.parent
		END DESC
) AS t
JOIN (
	SELECT @parent := 26
) AS tmp
LEFT JOIN wp_termmeta AS tm1 ON tm1.term_id = @parent AND tm1.meta_key = 'headline'
LEFT JOIN wp_termmeta AS tm2 ON tm2.term_id = @parent AND tm2.meta_key = 'enable_content_archive_settings'
WHERE t.term_id = @parent;