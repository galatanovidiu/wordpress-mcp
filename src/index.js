/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import './settings/style.css';
import { SettingsApp } from './settings';

/**
 * Render the settings page
 */
render( <SettingsApp />, document.getElementById( 'wordpress-mcp-settings' ) );
