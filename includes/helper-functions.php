<?php

/**
 * @file
 * All helper functions used in the plugin.
 *
 * Created by: Topsitemakers
 * http://www.topsitemakers.com/
 */

/**
 * Display fields.
 * If $admin_page is enabled, value will be fetched with get_option().
 */
function boilerplate_field($field, $print = true, $admin_page = false) {
  // Wrap.
  $output  = '<div class="form-item '.$field['class'].'">';
  // Label.
  if ($field['label']) {
    $output .= '<label for="'.$field['id'].'">'.$field['label'].'</label>';
  } else {
    $output .= '<label class="spacer"></label>';
  }
  // Field value - if it's admin field, fetch it with get_option()
  // "no-save" fields are checkboxes on edit templates page.
  if (!$field['no-save']) {
    $field['value'] = ($admin_page && $field['type']!='submit' && !$field['editor']) ? get_option(BOILERPLATE_SHORTNAME.$field['id']) : $field['value'];
  }
  // Field wrapper.
  $output .= '<div class="field">';
  // Field.
  switch ($field['type']) {
    case 'text':
      $output .= '<input type="text" id="'.$field['id'].'" name="'.$field['id'].'" value="'.$field['value'].'" />';
      break;
    case 'textarea':
      $output .= '<textarea id="'.$field['id'].'" name="'.$field['id'].'">'.$field['value'].'</textarea>';
      break;
    case 'select':
      $output .= '<select id="'.$field['id'].'" name="'.$field['id'].'">';
      foreach ($field['options'] as $value => $title) {
        $selected = ($field['value']==$value) ? ' selected="selected"' : '';
        $output .= '<option value="'.$value.'"'.$selected.'>'.$title.'</option>';
      }
      $output .= '</select>';
      break;
    case 'file':
      $output .= '<div class="file-preview" id="'.$field['id'].'-preview">';
      if ($field['value']) {
        $output .= '<img src="'.$field['value'].'" alt="'.$field['label'].'" />';
      } else {
        $output .= 'No image uploaded.';
      }
      $output .= '</div>';
      $output .= '<input type="text" id="'.$field['id'].'" name="'.$field['id'].'" value="'.$field['value'].'" />';
      $output .= '<a class="boilerplate-uploader button">Upload image</a>';
      break;
    case 'checkbox':
      $output .= '<input type="checkbox" id="'.$field['id'].'" name="'.$field['id'].'" value="'.$field['value'].'" />';
      break;
    case 'title':
      $output .= '<h4>'.$field['value'].'</h4>';
      break;
    case 'submit':
      $output .= '<input class="button" type="submit" id="'.$field['id'].'" value="'.$field['value'].'" />';
      break;
  }
  // Help.
  if ($field['help']) $output .= '<div class="help">'.$field['help'].'</div>';
  // Close field wrapper.
  $output .= '</div>';
  // Close wrap.
  $output .= '</div>';
  return $print ? print $output : $output;
}

/**
 * Closing for custom fields.
 */
function boilerplate_field_close($print = true) {
  return $print ? print '<div class="form-item-clear"></div>' : '<div class="form-item-clear"></div>';
}

/**
 * Generate admin page.
 */
function boilerplate_generate_admin_page($page) {
  // Wrap everything for styling.
  $output  = '<div id="boilerplate-admin-page"><div class="wrap">';
  // Page title and tabs.
  $output .= '<h2>'.$page['title'].'</h2>';
  // Page description.
  if ($page['description']) $output .= '<strong>'.$page['description'].'</strong>';
  // Page help text.
  if ($page['content']) $output .= '<p>'.$page['content'].'</p>';
  // Form.
  if ($page['form']) $output .= '<form action="'.htmlspecialchars($_SERVER['REQUEST_URI']).'" method="post">';
  // Page fields.
  if ($page['fieldset']) {
    foreach ($page['fieldset'] as $fieldset) {
      $output .= '<div class="boilerplate-fieldset-div metabox-holder">';
      $output .= '<div id="poststuff" class="postbox">';
      $output .= '<h3><span>'.$fieldset['title'];
      if (count($fieldset['tabs'])) {
        $output .= '<div class="boilerplate-tabs">';
        foreach ($fieldset['tabs'] as $tab) {
          $output .= '<a class="boilerplate-tab-trigger" rel="'.$tab['trigger'].'">'.$tab['title'].'</a>';
        }
        $output .= '</div>';
      }
      $output .= '</span></h3>';
      $output .= '<div class="inside">';
      foreach ($fieldset['fields'] as $field) $output .= boilerplate_field($field, false, true);
      $output .= boilerplate_field_close(false);
      $output .= '</div>';
      $output .= '</div>';
      $output .= '</div>';
    }
  }
  // Close form.
  if ($page['form']) $output .= '</form>';
  // Close wrappers.
  $output .= '</div></div>';
  print $output;
}

/**
 * Handle saving of admin settings data - mass.
 */
function boilerplate_admin_page_save_handle($message = NULL) {
  foreach ($_POST as $id => $value) update_option(BOILERPLATE_SHORTNAME.$id, $value);
  $message = $message ? $message : '<p>Options saved successfully.</p>';
  if ($_POST) print '<div id="message" class="updated">'.$message.'</div>';
}
