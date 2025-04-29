/**
 * WordPress dependencies
 */
import { Card, CardHeader, CardBody, Spinner } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { useState, useEffect } from '@wordpress/element';
import apiFetch from '@wordpress/api-fetch';

/**
 * Resources Tab Component
 */
const ResourcesTab = () => {
	const [ resources, setResources ] = useState( [] );
	const [ loading, setLoading ] = useState( true );
	const [ error, setError ] = useState( null );

	useEffect( () => {
		const fetchResources = async () => {
			try {
				setLoading( true );
				const response = await apiFetch( {
					path: '/wp/v2/wpmcp',
					method: 'POST',
					data: {
						jsonrpc: '2.0',
						method: 'resources/list',
						params: {},
					},
				} );

				if ( response && response.resources ) {
					setResources( response.resources );
				} else {
					setError(
						__( 'Failed to load resources data', 'wordpress-mcp' )
					);
				}
			} catch ( err ) {
				setError(
					__( 'Error loading resources: ', 'wordpress-mcp' ) +
						err.message
				);
			} finally {
				setLoading( false );
			}
		};

		fetchResources();
	}, [] );

	return (
		<Card>
			<CardHeader>
				<h2>{ __( 'Available Resources', 'wordpress-mcp' ) }</h2>
			</CardHeader>
			<CardBody>
				<p>
					{ __(
						'List of all available resources in the system.',
						'wordpress-mcp'
					) }
				</p>

				{ loading ? (
					<div className="wordpress-mcp-loading">
						<Spinner />
						<p>{ __( 'Loading resources...', 'wordpress-mcp' ) }</p>
					</div>
				) : error ? (
					<div className="wordpress-mcp-error">
						<p>{ error }</p>
					</div>
				) : resources.length === 0 ? (
					<p>
						{ __(
							'No resources are currently available.',
							'wordpress-mcp'
						) }
					</p>
				) : (
					<table className="wordpress-mcp-resources-table">
						<thead>
							<tr>
								<th>{ __( 'Name', 'wordpress-mcp' ) }</th>
								<th>{ __( 'URI', 'wordpress-mcp' ) }</th>
								<th>
									{ __( 'Description', 'wordpress-mcp' ) }
								</th>
							</tr>
						</thead>
						<tbody>
							{ resources.map( ( resource ) => (
								<tr key={ resource.name }>
									<td>
										<strong>{ resource.name }</strong>
									</td>
									<td>{ resource.uri }</td>
									<td>{ resource.description || '-' }</td>
								</tr>
							) ) }
						</tbody>
					</table>
				) }
			</CardBody>
		</Card>
	);
};

export default ResourcesTab;
