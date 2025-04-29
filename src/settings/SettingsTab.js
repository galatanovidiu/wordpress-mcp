/**
 * WordPress dependencies
 */
import {
	Card,
	CardHeader,
	CardBody,
	CardFooter,
	ToggleControl,
	Button,
	Spinner,
} from '@wordpress/components';
import { __ } from '@wordpress/i18n';

/**
 * Settings Tab Component
 */
const SettingsTab = ( {
	settings,
	onToggleChange,
	onSaveSettings,
	isSaving,
	strings,
} ) => {
	return (
		<Card>
			<CardHeader>
				<h2>{ __( 'General Settings', 'wordpress-mcp' ) }</h2>
			</CardHeader>
			<CardBody>
				<div className="setting-row">
					<ToggleControl
						label={
							strings.enableMcp ||
							__( 'Enable MCP functionality', 'wordpress-mcp' )
						}
						help={
							strings.enableMcpDescription ||
							__(
								'Toggle to enable or disable the MCP plugin functionality.',
								'wordpress-mcp'
							)
						}
						checked={ settings.enabled }
						onChange={ () => onToggleChange( 'enabled' ) }
					/>
				</div>

				<div className="setting-row">
					<ToggleControl
						label={
							strings.enableFeaturesAdapter ||
							__(
								'Enable WordPress Features Adapter',
								'wordpress-mcp'
							)
						}
						help={
							strings.enableFeaturesAdapterDescription ||
							__(
								'Enable or disable the WordPress Features Adapter. This option only works when MCP is enabled.',
								'wordpress-mcp'
							)
						}
						checked={ settings.features_adapter_enabled }
						onChange={ () =>
							onToggleChange( 'features_adapter_enabled' )
						}
						disabled={ ! settings.enabled }
					/>
				</div>
			</CardBody>
			<CardFooter>
				<Button
					variant="primary"
					onClick={ onSaveSettings }
					isBusy={ isSaving }
					disabled={ isSaving }
					className="button button-primary"
				>
					{ isSaving ? (
						<>
							<Spinner />
							{ __( 'Saving...', 'wordpress-mcp' ) }
						</>
					) : (
						strings.saveSettings ||
						__( 'Save Settings', 'wordpress-mcp' )
					) }
				</Button>
			</CardFooter>
		</Card>
	);
};

export default SettingsTab;
