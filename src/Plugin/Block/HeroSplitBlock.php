<?php

namespace Drupal\hero_banner\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\file\Entity\File;

/**
 * Provides a 'HeroSplitBlock' block.
 *
 * @Block(
 *  id = "split_banner",
 *  admin_label = @Translation("Split block"),
 * )
 */
class HeroSplitBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [] + parent::defaultConfiguration();
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form['titulo'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Title'),
      '#default_value' => $this->configuration['titulo'],
      '#maxlength' => 64,
      '#size' => 64,
      '#weight' => '1',
    ];
    $form['intro'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Introduction'),
      '#default_value' => $this->configuration['intro'],
      '#maxlength' => 512,
      '#weight' => '2',
    ];
    $form['texto'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Text'),
      '#default_value' => $this->configuration['texto'],
      '#maxlength' => 512,
      '#weight' => '2',
    ];
    $form['texto_link'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Link text'),
      '#default_value' => $this->configuration['texto_link'],
      '#maxlength' => 128,
      '#size' => 64,
      '#weight' => '3',
    ];
    $form['link'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Link'),
      '#default_value' => $this->configuration['link'],
      '#weight' => '4',
    ];
    $form['layout'] = [
      '#type' => 'select',
      '#title' => $this->t('Layout'),
      '#default_value' => $this->configuration['layout'],
      '#options' => [
        'left' => t('Image Left'),
        'right' => t('Image Right')
      ],
      '#weight' => '4',
    ];
    $form['imagen'] = [
      '#type' => 'managed_file',
      '#title' => $this->t('Image'),
      '#default_value' => $this->configuration['imagen'],
      '#weight' => '10',
      '#upload_validators' => ['file_validate_extensions' => 'jpg', 'png', 'jpeg',],
      '#upload_location' => 'public://banner/',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    // Save image as permanent.
    $image = $form_state->getValue('imagen');
    if ($image != $this->configuration['imagen']) {
      if (!empty($image[0])) {
        $file = File::load($image[0]);
        $file->setPermanent();
        $file->save();
      }
    }
    $this->configuration['titulo'] = $form_state->getValue('titulo');
    $this->configuration['texto'] = $form_state->getValue('texto');
    $this->configuration['texto_link'] = $form_state->getValue('texto_link');
    $this->configuration['link'] = $form_state->getValue('link');
    $this->configuration['intro'] = $form_state->getValue('intro');
    $this->configuration['layout'] = $form_state->getValue('layout');
    $this->configuration['imagen'] = $form_state->getValue('imagen');
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $image = $this->configuration['imagen'];
    if (!empty($image[0])) {
      if ($file = File::load($image[0])) {
        $uri = $file->getFileUri();
      }
    }
    $build = [];
    $build['split_block'] = [
      '#theme' => 'split_banner',
      '#titulo' => $this->configuration['titulo'],
      '#texto' => $this->configuration['texto'],
      '#texto_link' => $this->configuration['texto_link'],
      '#link' => $this->configuration['link'],
      '#intro' => $this->configuration['intro'],
      '#layout' => $this->configuration['layout'],
      '#imagen' => file_url_transform_relative(file_create_url($uri)),
    ];

    return $build;
  }

}