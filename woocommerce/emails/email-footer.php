<?php
/**
 * Pied des e-mails WooCommerce (IT-8 – US-8.2)
 *
 * Copie de woocommerce/templates/emails/email-footer.php (version 10.4.0)
 * avec le pied marron du site : texte réglé dans WooCommerce > Réglages > E-mails
 * et liens vers la boutique et l'espace client.
 *
 * @package WooCommerce\Templates\Emails
 * @version 10.4.0
 */

defined( 'ABSPATH' ) || exit;

$email = $email ?? null;

$texte_pied = get_option( 'woocommerce_email_footer_text' );
$texte_pied = apply_filters( 'woocommerce_email_footer_text', $texte_pied, $email );
?>
																		</div>
																	</td>
																</tr>
															</table>
														</td>
													</tr>
												</table>
											</td>
										</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td align="center" valign="top">
									<!-- Pied de l'e-mail -->
									<table border="0" cellpadding="10" cellspacing="0" width="100%" id="template_footer" role="presentation">
										<tr>
											<td valign="top" id="credit">
												<?php echo wp_kses_post( wpautop( wptexturize( $texte_pied ) ) ); ?>
												<p>
													<a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>">Commander</a>
													&nbsp;·&nbsp;
													<a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>">Mon compte</a>
												</p>
											</td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
					</div>
				</td>
				<td></td>
			</tr>
		</table>
	</body>
</html>
