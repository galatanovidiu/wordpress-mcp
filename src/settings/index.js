/**
 * WordPress dependencies
 */
import { useState, useEffect } from '@wordpress/element';
import { Notice, TabPanel } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

// Import the extracted components
import SettingsTab from './SettingsTab';
import ToolsTab from './ToolsTab';
import ResourcesTab from './ResourcesTab';
import PromptsTab from './PromptsTab';

/**
 * Settings App Component
 */
export const SettingsApp = () => {
	// State for settings
	const [ settings, setSettings ] = useState( {
		enabled: false,
		features_adapter_enabled: false,
	} );

	// State for UI
	const [ isSaving, setIsSaving ] = useState( false );
	const [ notice, setNotice ] = useState( null );

	// Load settings on component mount
	useEffect( () => {
		if (
			window.wordpressMcpSettings &&
			window.wordpressMcpSettings.settings
		) {
			setSettings( window.wordpressMcpSettings.settings );
		}
	}, [] );

	// Handle toggle changes
	const handleToggleChange = ( key ) => {
		setSettings( ( prevSettings ) => ( {
			...prevSettings,
			[ key ]: ! prevSettings[ key ],
		} ) );
	};

	// Handle save settings
	const handleSaveSettings = () => {
		setIsSaving( true );
		setNotice( null );

		// Create form data for AJAX request
		const formData = new FormData();
		formData.append( 'action', 'wordpress_mcp_save_settings' );
		formData.append( 'nonce', window.wordpressMcpSettings.nonce );
		formData.append( 'settings', JSON.stringify( settings ) );

		// Send AJAX request
		fetch( ajaxurl, {
			method: 'POST',
			body: formData,
			credentials: 'same-origin',
		} )
			.then( ( response ) => response.json() )
			.then( ( data ) => {
				setIsSaving( false );
				if ( data.success ) {
					setNotice( {
						status: 'success',
						message:
							data.data.message ||
							window.wordpressMcpSettings.strings.settingsSaved,
					} );
				} else {
					setNotice( {
						status: 'error',
						message:
							data.data.message ||
							window.wordpressMcpSettings.strings.settingsError,
					} );
				}
			} )
			.catch( ( error ) => {
				setIsSaving( false );
				setNotice( {
					status: 'error',
					message: window.wordpressMcpSettings.strings.settingsError,
				} );
				console.error( 'Error saving settings:', error );
			} );
	};

	// Get localized strings
	const strings = window.wordpressMcpSettings
		? window.wordpressMcpSettings.strings
		: {};

	const tabs = [
		{
			name: 'settings',
			title: __( 'Settings', 'wordpress-mcp' ),
			className: 'wordpress-mcp-settings-tab',
		},
		{
			name: 'tools',
			title: __( 'Tools', 'wordpress-mcp' ),
			className: 'wordpress-mcp-tools-tab',
		},
		{
			name: 'resources',
			title: __( 'Resources', 'wordpress-mcp' ),
			className: 'wordpress-mcp-resources-tab',
		},
		{
			name: 'prompts',
			title: __( 'Prompts', 'wordpress-mcp' ),
			className: 'wordpress-mcp-prompts-tab',
		},
	];

	return (
		<div className="wordpress-mcp-settings">
			{ notice && (
				<Notice
					status={ notice.status }
					isDismissible={ true }
					onRemove={ () => setNotice( null ) }
					className={ `notice notice-${ notice.status } is-dismissible` }
				>
					{ notice.message }
				</Notice>
			) }

			<TabPanel className="wordpress-mcp-tabs" tabs={ tabs }>
				{ ( tab ) => {
					switch ( tab.name ) {
						case 'settings':
							return (
								<SettingsTab
									settings={ settings }
									onToggleChange={ handleToggleChange }
									onSaveSettings={ handleSaveSettings }
									isSaving={ isSaving }
									strings={ strings }
								/>
							);
						case 'tools':
							return <ToolsTab />;
						case 'resources':
							return <ResourcesTab />;
						case 'prompts':
							return <PromptsTab />;
						default:
							return null;
					}
				} }
			</TabPanel>
		</div>
	);
};
