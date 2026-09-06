import React from 'react';
import {
  AbsoluteFill,
  interpolate,
  staticFile,
} from 'remotion';
import { Audio } from '@remotion/media';
import { TransitionSeries, linearTiming } from '@remotion/transitions';
import { fade } from '@remotion/transitions/fade';

import { Scene1Hook } from './scenes/Scene1Hook';
import { Scene2Identity } from './scenes/Scene2Identity';
import { Scene3Dimensions } from './scenes/Scene3Dimensions';
import { Scene4Advantage } from './scenes/Scene4Advantage';
import { Scene5CallToAction } from './scenes/Scene5CallToAction';

export const SCENE_DURATIONS = {
  scene1: 360,
  scene2: 520,
  scene3: 460,
  scene4: 410,
  scene5: 330,
};

export const TRANSITION_DURATION = 20;

export const TOTAL_DURATION_IN_FRAMES =
  SCENE_DURATIONS.scene1 +
  SCENE_DURATIONS.scene2 +
  SCENE_DURATIONS.scene3 +
  SCENE_DURATIONS.scene4 +
  SCENE_DURATIONS.scene5 -
  4 * TRANSITION_DURATION; // 2000 frames = 66.67 seconds

export const RomvillCommercial: React.FC = () => {
  return (
    <AbsoluteFill style={{ backgroundColor: '#06090F' }}>
      {/* Global Cinematic Luxury Ambient Soundtrack */}
      <Audio
        src={staticFile('audio/luxury_ambient_theme.wav')}
        volume={(f) =>
          interpolate(
            f,
            [0, 40, TOTAL_DURATION_IN_FRAMES - 90, TOTAL_DURATION_IN_FRAMES],
            [0, 0.25, 0.25, 0],
            {
              extrapolateLeft: 'clamp',
              extrapolateRight: 'clamp',
            }
          )
        }
      />

      {/* Cinematic Scene Sequences with Smooth Crossfade Dissolves */}
      <TransitionSeries>
        {/* Scene 1: The Blind Spot (0s - 12s) */}
        <TransitionSeries.Sequence durationInFrames={SCENE_DURATIONS.scene1}>
          <Scene1Hook />
        </TransitionSeries.Sequence>

        <TransitionSeries.Transition
          presentation={fade()}
          timing={linearTiming({ durationInFrames: TRANSITION_DURATION })}
        />

        {/* Scene 2: Who is Romvill? Independence & Trust (12s - 29s) */}
        <TransitionSeries.Sequence durationInFrames={SCENE_DURATIONS.scene2}>
          <Scene2Identity />
        </TransitionSeries.Sequence>

        <TransitionSeries.Transition
          presentation={fade()}
          timing={linearTiming({ durationInFrames: TRANSITION_DURATION })}
        />

        {/* Scene 3: The 5 Core Forensic Dimensions (29s - 44s) */}
        <TransitionSeries.Sequence durationInFrames={SCENE_DURATIONS.scene3}>
          <Scene3Dimensions />
        </TransitionSeries.Sequence>

        <TransitionSeries.Transition
          presentation={fade()}
          timing={linearTiming({ durationInFrames: TRANSITION_DURATION })}
        />

        {/* Scene 4: Families & Investors · Packages from €149 (44s - 57s) */}
        <TransitionSeries.Sequence durationInFrames={SCENE_DURATIONS.scene4}>
          <Scene4Advantage />
        </TransitionSeries.Sequence>

        <TransitionSeries.Transition
          presentation={fade()}
          timing={linearTiming({ durationInFrames: TRANSITION_DURATION })}
        />

        {/* Scene 5: High-Converting Finale & CTA (57s - 67s) */}
        <TransitionSeries.Sequence durationInFrames={SCENE_DURATIONS.scene5}>
          <Scene5CallToAction />
        </TransitionSeries.Sequence>
      </TransitionSeries>
    </AbsoluteFill>
  );
};
